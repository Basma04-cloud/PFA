<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\Compte;
use App\Models\Objectif;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Utiliser les vraies données
            $data = $this->getRealData();
            
            // Ajouter des statistiques supplémentaires
            $data['nombreComptes'] = Compte::where('user_id', $user->id)->count();
            $data['nombreTransactions'] = Transaction::where('user_id', $user->id)->count();
            $data['nombreObjectifs'] = Objectif::where('user_id', $user->id)->count();
            
            // Calculer l'économie du mois
            $data['economieDuMois'] = $data['revenusDuMois'] - $data['depensesDuMois'];
            
            // Objectifs en cours
            $data['objectifsActifs'] = Objectif::where('user_id', $user->id)
                ->where('statut', 'actif')
                ->orderBy('date_echeance', 'asc')
                ->limit(3)
                ->get();

            // Données pour les graphiques - CORRECTION ICI
            $data['objectifs'] = $this->getObjectifsForChart();

            return view('dashboard', $data);

        } catch (\Exception $e) {
            Log::error('Erreur dashboard', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return view('dashboard', [
                'soldeTotal' => 0,
                'depensesDuMois' => 0,
                'revenusDuMois' => 0,
                'economieDuMois' => 0,
                'dernieresTransactions' => collect(),
                'nombreComptes' => 0,
                'nombreTransactions' => 0,
                'nombreObjectifs' => 0,
                'objectifsActifs' => collect(),
                'objectifs' => collect(),
                'error' => 'Erreur lors du chargement: ' . $e->getMessage()
            ]);
        }
    }

    private function getRealData()
    {
        $user = Auth::user();
        
        try {
            $soldeTotal = Compte::where('user_id', $user->id)->sum('solde') ?? 0;
            
            $depensesDuMois = abs(Transaction::where('user_id', $user->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('montant', '<', 0)
                ->sum('montant')) ?? 0;
            
            $revenusDuMois = Transaction::where('user_id', $user->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('montant', '>', 0)
                ->sum('montant') ?? 0;
            
            $dernieresTransactions = Transaction::where('user_id', $user->id)
                ->with('compte')
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get();

            return compact('soldeTotal', 'depensesDuMois', 'revenusDuMois', 'dernieresTransactions');
            
        } catch (\Exception $e) {
            Log::error('Erreur getRealData', ['error' => $e->getMessage()]);
            return [
                'soldeTotal' => 0,
                'depensesDuMois' => 0,
                'revenusDuMois' => 0,
                'dernieresTransactions' => collect()
            ];
        }
    }

    // Nouvelle méthode pour les objectifs du graphique
    private function getObjectifsForChart()
    {
        try {
            $user = Auth::user();
            
            return Objectif::where('user_id', $user->id)
                ->where('statut', 'actif')
                ->get()
                ->map(function($objectif) {
                    $montantVise = $objectif->montant_vise ?? 0;
                    $montantAtteint = $objectif->montant_atteint ?? 0;
                    
                    $progression = $montantVise > 0 
                        ? ($montantAtteint / $montantVise) * 100 
                        : 0;
                
                    return [
                        'nom' => Str::limit($objectif->nom ?? 'Objectif', 15),
                        'progression' => round($progression, 1)
                    ];
                });
        } catch (\Exception $e) {
            Log::error('Erreur getObjectifsForChart', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    // API pour les graphiques - données mensuelles - CORRIGÉ
    /*public function getChartData()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }
            
            $monthsData = [];
            
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                
                $revenus = Transaction::where('user_id', $user->id)
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->where('montant', '>', 0)
                    ->sum('montant') ?? 0;
                
                $depenses = abs(Transaction::where('user_id', $user->id)
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->where('montant', '<', 0)
                    ->sum('montant')) ?? 0;
                
                $monthsData[] = [
                    'mois' => $date->format('M Y'),
                    'revenus' => (float) $revenus,
                    'depenses' => (float) $depenses
                ];
            }
            
            Log::info('Chart data generated', ['data' => $monthsData, 'user_id' => $user->id]);
            
            return response()->json([
                'success' => true,
                'data' => $monthsData
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur getChartData', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            // Retourner des données par défaut en cas d'erreur - MAIS AVEC SUCCESS FALSE
            $defaultData = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $defaultData[] = [
                    'mois' => $date->format('M Y'),
                    'revenus' => 0,
                    'depenses' => 0
                ];
            }
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'data' => $defaultData
            ]);
        }
    }*/

    // API pour les dépenses par catégorie - CORRIGÉ
    /*public function getExpensesData()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }
        
            $depensesParCategorie = Transaction::where('user_id', $user->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('montant', '<', 0)
                ->whereNotNull('categorie') // AJOUTÉ: éviter les catégories NULL
                ->selectRaw('categorie, SUM(ABS(montant)) as total')
                ->groupBy('categorie')
                ->orderBy('total', 'desc')
                ->get()
                ->map(function($item) {
                    return [
                        'categorie' => $item->categorie ?? 'Non défini',
                        'montant' => (float) ($item->total ?? 0)
                    ];
                });

            Log::info('Expenses data generated', ['data' => $depensesParCategorie, 'user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'data' => $depensesParCategorie
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur getExpensesData', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'data' => []
            ]);
        }
    }*/

    // API pour les objectifs - CORRIGÉ
    public function getObjectifsData()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }
        
            $objectifs = Objectif::where('user_id', $user->id)
                ->where('statut', 'actif')
                ->whereNotNull('nom') // AJOUTÉ: éviter les noms NULL
                ->get()
                ->map(function($objectif) {
                    $montantVise = (float) ($objectif->montant_vise ?? 0);
                    $montantAtteint = (float) ($objectif->montant_atteint ?? 0);
                    
                    $progression = $montantVise > 0 
                        ? ($montantAtteint / $montantVise) * 100 
                        : 0;
                
                    return [
                        'nom' => Str::limit($objectif->nom ?? 'Objectif', 15),
                        'progression' => round($progression, 1),
                        'montant_vise' => $montantVise,
                        'montant_atteint' => $montantAtteint
                    ];
                });

            Log::info('Objectifs data generated', ['data' => $objectifs, 'user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'data' => $objectifs
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur getObjectifsData', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    // API pour les comptes - CORRIGÉ
    /*public function getComptesData()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }
        
            $comptes = Compte::where('user_id', $user->id)
                ->whereNotNull('nom_compte') // AJOUTÉ: éviter les noms NULL
                ->get()
                ->map(function($compte) {
                    return [
                        'nom' => Str::limit($compte->nom_compte ?? 'Compte', 15),
                        'solde' => (float) ($compte->solde ?? 0),
                        'type' => $compte->type_compte ?? 'Courant'
                    ];
                });

            Log::info('Comptes data generated', ['data' => $comptes, 'user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'data' => $comptes
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur getComptesData', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'data' => []
            ]);
        }
    }*/

    // Méthode de debug pour tester toutes les APIs - AMÉLIORÉE
    public function debugApis()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }
            
            $debug = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'timestamp' => now()->toDateTimeString(),
                'apis' => [
                    'chart_data' => $this->testApi('getChartData'),
                    'expenses_data' => $this->testApi('getExpensesData'),
                    'objectifs_data' => $this->testApi('getObjectifsData'),
                    'comptes_data' => $this->testApi('getComptesData'),
                ]
            ];
            
            return response()->json($debug);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    // NOUVELLE MÉTHODE: Helper pour tester chaque API
    private function testApi($methodName)
    {
        try {
            $response = $this->$methodName();
            $data = $response->getData(true);
            
            return [
                'status' => 'success',
                'has_data' => isset($data['data']) && !empty($data['data']),
                'data_count' => isset($data['data']) ? count($data['data']) : 0,
                'sample' => isset($data['data']) ? (is_array($data['data']) ? array_slice($data['data'], 0, 2) : $data['data']) : null
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }
}