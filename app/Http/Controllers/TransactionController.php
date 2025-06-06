<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\Compte;
// Pas besoin d'importer NotificationService car les observers s'en chargent

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->with('compte')
            ->latest('date')
            ->get();
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $comptes = Compte::where('user_id', Auth::id())->get();
        
        // Debug: Vérifier s'il y a des comptes
        if ($comptes->isEmpty()) {
            return redirect()->route('comptes.create')
                ->with('error', 'Vous devez d\'abord créer un compte avant d\'ajouter une transaction.');
        }
        
        return view('transactions.create', compact('comptes'));
    }

    public function store(Request $request)
    {
        // Debug: Log des données reçues
        Log::info('Transaction store attempt', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        // Validation avec vérification personnalisée
        $request->validate([
            'nom' => 'required|string|max:255',
            'compte_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    // Vérifier que le compte existe ET appartient à l'utilisateur
                    $compte = Compte::where('id', $value)
                        ->where('user_id', Auth::id())
                        ->first();
                    
                    if (!$compte) {
                        $fail('Le compte sélectionné n\'existe pas ou ne vous appartient pas.');
                    }
                }
            ],
            'type' => 'required|in:revenu,depense,transfert',
            'montant' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'categorie' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        // Double vérification du compte
        $compte = Compte::where('id', $request->compte_id)
                        ->where('user_id', Auth::id())
                        ->first();

        if (!$compte) {
            Log::warning('Compte non trouvé après validation', [
                'user_id' => Auth::id(),
                'compte_id' => $request->compte_id,
                'available_comptes' => Compte::where('user_id', Auth::id())->pluck('id')->toArray()
            ]);
            
            return back()->withErrors([
                'compte_id' => 'Le compte sélectionné n\'existe pas ou ne vous appartient pas.'
            ])->withInput();
        }

        try {
            DB::transaction(function () use ($request, $compte) {
                // Déterminer le montant selon le type
                $montant = $request->montant;
                if ($request->type === 'depense') {
                    $montant = -abs($montant); // Négatif pour les dépenses
                } else {
                    $montant = abs($montant); // Positif pour les revenus
                }

                // Debug: Log avant création
                Log::info('Creating transaction', [
                    'user_id' => Auth::id(),
                    'compte_id' => $request->compte_id,
                    'compte_exists' => $compte ? 'yes' : 'no',
                    'montant' => $montant,
                    'type' => $request->type
                ]);

                // Créer la transaction avec vérification explicite
                $transactionData = [
                    'nom' => $request->nom,
                    'compte_id' => (int) $request->compte_id, // S'assurer que c'est un entier
                    'type' => $request->type,
                    'montant' => $montant,
                    'date' => $request->date,
                    'categorie' => $request->categorie,
                    'description' => $request->description,
                    'user_id' => Auth::id(),
                ];

                Log::info('Transaction data to insert', $transactionData);

                $transaction = Transaction::create($transactionData);
                // Les notifications seront automatiquement envoyées via l'Observer

                // Mettre à jour le solde du compte (sauf pour les transferts)
                if ($request->type !== 'transfert') {
                    $compte->solde += $montant;
                    $compte->save();
                    // Les notifications de solde seront envoyées via l'Observer
                }

                Log::info('Transaction created successfully', ['transaction_id' => $transaction->id]);
            });

            return redirect()->route('transactions.index')->with('success', 'Transaction ajoutée avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la transaction', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return back()->withErrors([
                'general' => 'Une erreur est survenue lors de la création de la transaction: ' . $e->getMessage()
            ])->withInput();
        }
    }

    // Méthode de debug pour diagnostiquer le problème
    public function debug()
    {
        $user_id = Auth::id();
        
        // Vérifier les tables existantes
        $tables = DB::select("SHOW TABLES");
        $tableNames = array_map(function($table) {
            return array_values((array)$table)[0];
        }, $tables);
        
        // Vérifier les comptes dans les deux tables possibles
        $comptesInCompte = [];
        $comptesInComptes = [];
        
        try {
            $comptesInCompte = DB::table('compte')->where('user_id', $user_id)->get();
        } catch (Exception $e) {
            // Table compte n'existe pas
        }
        
        try {
            $comptesInComptes = DB::table('comptes')->where('user_id', $user_id)->get();
        } catch (Exception $e) {
            // Table comptes n'existe pas
        }
        
        // Vérifier les contraintes
        $constraints = DB::select("
            SELECT 
                CONSTRAINT_NAME,
                TABLE_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'transactions'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        return response()->json([
            'user_id' => $user_id,
            'tables_in_db' => $tableNames,
            'comptes_in_compte_table' => $comptesInCompte,
            'comptes_in_comptes_table' => $comptesInComptes,
            'foreign_key_constraints' => $constraints,
            'eloquent_comptes' => Compte::where('user_id', $user_id)->get()
        ]);
    }

    // Autres méthodes...
    public function edit(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);
        $comptes = Compte::where('user_id', Auth::id())->get();
        return view('transactions.edit', compact('transaction', 'comptes'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        // Même logique que store...
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        try {
            DB::transaction(function () use ($transaction) {
                $compte = $transaction->compte;

                // Annuler l'effet de la transaction (sauf transferts)
                if ($transaction->type !== 'transfert') {
                    $compte->solde -= $transaction->montant;
                    $compte->save();
                }

                $transaction->delete();
            });

            return redirect()->route('transactions.index')->with('success', 'Transaction supprimée avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la transaction', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'general' => 'Une erreur est survenue lors de la suppression.'
            ]);
        }
    }

    private function authorizeTransaction(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
