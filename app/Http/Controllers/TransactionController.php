<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Compte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        // Récupérer les transactions de l'utilisateur connecté
        $transactions = Transaction::where('user_id', Auth::id())
                                 ->with('compte')
                                 ->orderBy('date', 'desc')
                                 ->orderBy('created_at', 'desc')
                                 ->get();

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        // Récupérer les comptes de l'utilisateur pour le formulaire
        $comptes = Compte::where('user_id', Auth::id())->get();

        return view('transactions.create', compact('comptes'));
    }

    public function store(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'montant' => 'required|numeric',
            'date' => 'required|date',
            'categorie' => 'required|string',
            'compte_id' => 'required|exists:compte,id',
            'type' => 'required|in:depense,revenu,transfert',
            'description' => 'nullable|string|max:1000',
        ]);

        // Vérifier que le compte appartient à l'utilisateur
        $compte = Compte::where('id', $validatedData['compte_id'])
                       ->where('user_id', Auth::id())
                       ->firstOrFail();

        // Créer la transaction
        $transaction = Transaction::create([
            'nom' => $validatedData['nom'],
            'montant' => $validatedData['montant'],
            'date' => $validatedData['date'],
            'categorie' => $validatedData['categorie'],
            'type' => $validatedData['type'],
            'description' => $validatedData['description'],
            'compte_id' => $validatedData['compte_id'],
            'user_id' => Auth::id(),
        ]);

        // Mettre à jour le solde du compte
        if ($validatedData['type'] === 'depense') {
            $compte->solde -= abs($validatedData['montant']);
        } elseif ($validatedData['type'] === 'revenu') {
            $compte->solde += abs($validatedData['montant']);
        }
        $compte->save();

        return redirect()->route('transactions.index')->with('success', 'Transaction créée avec succès !');
    }

    public function show($id)
    {
        // Récupérer la transaction de l'utilisateur connecté
        $transaction = Transaction::where('user_id', Auth::id())
                                 ->where('id', $id)
                                 ->with('compte')
                                 ->firstOrFail();

        return view('transactions.show', compact('transaction'));
    }

    public function edit($id)
    {
        // Récupérer la transaction et les comptes
        $transaction = Transaction::where('user_id', Auth::id())
                                 ->where('id', $id)
                                 ->firstOrFail();
        
        $comptes = Compte::where('user_id', Auth::id())->get();

        return view('transactions.edit', compact('transaction', 'comptes'));
    }

    public function update(Request $request, $id)
    {
        // Validation des données
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'montant' => 'required|numeric',
            'date' => 'required|date',
            'categorie' => 'required|string',
            'compte_id' => 'required|exists:compte,id',
            'type' => 'required|in:depense,revenu,transfert',
            'description' => 'nullable|string|max:1000',
        ]);

        // Récupérer la transaction
        $transaction = Transaction::where('user_id', Auth::id())
                                 ->where('id', $id)
                                 ->firstOrFail();

        // Récupérer l'ancien et le nouveau compte
        $ancienCompte = $transaction->compte;
        $nouveauCompte = Compte::where('id', $validatedData['compte_id'])
                              ->where('user_id', Auth::id())
                              ->firstOrFail();

        // Annuler l'effet de l'ancienne transaction sur l'ancien compte
        if ($transaction->type === 'depense') {
            $ancienCompte->solde += abs($transaction->montant);
        } elseif ($transaction->type === 'revenu') {
            $ancienCompte->solde -= abs($transaction->montant);
        }
        $ancienCompte->save();

        // Mettre à jour la transaction
        $transaction->update($validatedData);

        // Appliquer l'effet de la nouvelle transaction sur le nouveau compte
        if ($validatedData['type'] === 'depense') {
            $nouveauCompte->solde -= abs($validatedData['montant']);
        } elseif ($validatedData['type'] === 'revenu') {
            $nouveauCompte->solde += abs($validatedData['montant']);
        }
        $nouveauCompte->save();

        return redirect()->route('transactions.index')->with('success', 'Transaction mise à jour avec succès !');
    }

    public function destroy($id)
    {
        // Récupérer la transaction
        $transaction = Transaction::where('user_id', Auth::id())
                                 ->where('id', $id)
                                 ->firstOrFail();

        // Annuler l'effet de la transaction sur le compte
        $compte = $transaction->compte;
        if ($transaction->type === 'depense') {
            $compte->solde += abs($transaction->montant);
        } elseif ($transaction->type === 'revenu') {
            $compte->solde -= abs($transaction->montant);
        }
        $compte->save();

        // Supprimer la transaction
        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaction supprimée avec succès !');
    }
}
