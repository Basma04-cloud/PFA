<?php

namespace App\Http\Controllers;

use App\Models\Compte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CompteController extends Controller
{
    // Affiche tous les comptes de l'utilisateur connecté
    public function index()
    {
        try {
            $comptes = Compte::where('user_id', Auth::id())
                             ->orderBy('created_at', 'desc')
                             ->get();

            return view('comptes.index', compact('comptes'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des comptes', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return view('comptes.index', [
                'comptes' => collect(),
                'error' => 'Erreur lors du chargement des comptes'
            ]);
        }
    }

    // Affiche le formulaire de création
    public function create()
    {
        try {
            $tableExists = Schema::hasTable('compte');
            if (!$tableExists) {
                return redirect()->route('dashboard')->withErrors([
                    'general' => 'La table des comptes n\'existe pas. Veuillez exécuter les migrations.'
                ]);
            }

            $hasDescription = Schema::hasColumn('compte', 'description');
            
            return view('comptes.create', compact('hasDescription'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage du formulaire de création', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('dashboard')->withErrors([
                'general' => 'Erreur lors de l\'affichage du formulaire'
            ]);
        }
    }
    public function show($id)
{
    $compte = DB::table('compte')->where('id', $id)->first();

    if ($compte) {
        return response()->json($compte);
    } else {
        return response()->json(['message' => 'Compte non trouvé'], 404);
    }
}

    // Méthode store CORRIGÉE avec logging détaillé
    public function store(Request $request)
    {
        Log::info('=== DÉBUT CRÉATION COMPTE ===', [
            'user_id' => Auth::id(),
            'request_all' => $request->all(),
            'request_type_compte' => $request->input('type_compte'),
            'request_method' => $request->method()
        ]);

        try {
            // Vérifier que l'utilisateur est connecté
            if (!Auth::check()) {
                return redirect()->route('login')->withErrors([
                    'general' => 'Vous devez être connecté pour créer un compte.'
                ]);
            }

            // Vérifier la structure de la table
            $this->verifierStructureTable();

            // Validation SANS user_id (car il sera ajouté automatiquement)
            $rules = Compte::getValidationRules();
            
            Log::info('Règles de validation utilisées', $rules);
            
            $validatedData = $request->validate($rules);

            Log::info('Données validées', [
                'validated_data' => $validatedData,
                'type_compte_validated' => $validatedData['type_compte'] ?? 'NON_DÉFINI'
            ]);

            // VÉRIFICATION EXPLICITE DU TYPE
            $typeCompte = $validatedData['type_compte'];
            Log::info('Type de compte extrait', [
                'type_original' => $request->input('type_compte'),
                'type_validated' => $typeCompte,
                'types_autorisés' => ['courant', 'epargne', 'credit', 'investissement']
            ]);

            // Préparer les données pour la création avec user_id automatique
            $compteData = [
                'nom_compte' => $validatedData['nom_compte'],
                'type_compte' => $typeCompte, // UTILISER LA VARIABLE EXPLICITE
                'solde' => $validatedData['solde'] ?? 0,
                'user_id' => Auth::id(),
            ];

            // Ajouter description seulement si la colonne existe et est fournie
            if (Schema::hasColumn('compte', 'description') && !empty($validatedData['description'])) {
                $compteData['description'] = $validatedData['description'];
            }

            Log::info('Données préparées pour insertion', [
                'compte_data' => $compteData,
                'type_compte_final' => $compteData['type_compte']
            ]);

            // DOUBLE VÉRIFICATION avant insertion
            if (!in_array($compteData['type_compte'], ['courant', 'epargne', 'credit', 'investissement'])) {
                Log::error('Type de compte invalide détecté', [
                    'type_reçu' => $compteData['type_compte'],
                    'types_valides' => ['courant', 'epargne', 'credit', 'investissement']
                ]);
                
                return back()->withErrors([
                    'type_compte' => 'Type de compte invalide: ' . $compteData['type_compte']
                ])->withInput();
            }

            // Utiliser une transaction pour la création
            $compte = DB::transaction(function () use ($compteData) {
                Log::info('Insertion en base avec données', $compteData);
                
                $compte = Compte::create($compteData);
                
                Log::info('Compte créé, vérification immédiate', [
                    'compte_id' => $compte->id,
                    'type_en_base' => $compte->type_compte,
                    'type_attendu' => $compteData['type_compte']
                ]);
                
                // VÉRIFICATION IMMÉDIATE après création
                $compteVerif = Compte::find($compte->id);
                Log::info('Vérification post-création', [
                    'compte_id' => $compteVerif->id,
                    'type_stocké' => $compteVerif->type_compte,
                    'type_formatté' => $compteVerif->type_formatte ?? 'N/A'
                ]);
                
                return $compte;
            });

            Log::info('=== COMPTE CRÉÉ AVEC SUCCÈS ===', [
                'compte_id' => $compte->id,
                'type_final' => $compte->type_compte
            ]);

            return redirect()->route('comptes.index')->with('success', 'Compte créé avec succès.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Erreur de validation', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);

            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du compte', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);

            return back()->withErrors([
                'general' => 'Une erreur est survenue lors de la création du compte: ' . $e->getMessage()
            ])->withInput();
        }
    }

    // Méthode pour vérifier la structure de la table
    private function verifierStructureTable()
    {
        if (!Schema::hasTable('compte')) {
            throw new \Exception('La table "compte" n\'existe pas. Veuillez exécuter les migrations.');
        }

        $requiredColumns = ['nom_compte', 'type_compte', 'solde', 'user_id'];
        foreach ($requiredColumns as $column) {
            if (!Schema::hasColumn('compte', $column)) {
                throw new \Exception("La colonne '{$column}' est manquante dans la table 'compte'.");
            }
        }

        Log::info('Structure de la table vérifiée avec succès');
    }

    // Affiche le formulaire de modification
    public function edit(Compte $compte)
    {
        // Vérifie que le compte appartient à l'utilisateur
        if ($compte->user_id !== Auth::id()) {
            abort(403);
        }

        $hasDescription = Schema::hasColumn('compte', 'description');
        return view('comptes.edit', compact('compte', 'hasDescription'));
    }

    // Met à jour un compte
    public function update(Request $request, Compte $compte)
    {
        if ($compte->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            // Validation SANS user_id pour la mise à jour
            $rules = Compte::getValidationRules();
            $validatedData = $request->validate($rules);

            // Préparer les données pour la mise à jour
            $updateData = [
                'nom_compte' => $validatedData['nom_compte'],
                'type_compte' => $validatedData['type_compte'],
                'solde' => $validatedData['solde'] ?? $compte->solde,
            ];

            // Ajouter description seulement si la colonne existe
            if (Schema::hasColumn('compte', 'description') && isset($validatedData['description'])) {
                $updateData['description'] = $validatedData['description'];
            }

            $compte->update($updateData);

            return redirect()->route('comptes.index')->with('success', 'Compte mis à jour avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du compte', [
                'error' => $e->getMessage(),
                'compte_id' => $compte->id
            ]);

            return back()->withErrors([
                'general' => 'Une erreur est survenue lors de la mise à jour.'
            ])->withInput();
        }
    }

    // Supprime un compte
    public function destroy(Compte $compte)
    {
        if ($compte->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            // Vérifier s'il y a des transactions liées
            $transactionsCount = $compte->transactions()->count();
            
            if ($transactionsCount > 0) {
                return back()->withErrors([
                    'general' => "Impossible de supprimer ce compte car il contient {$transactionsCount} transaction(s). Supprimez d'abord les transactions."
                ]);
            }

            $compte->delete();

            return redirect()->route('comptes.index')->with('success', 'Compte supprimé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du compte', [
                'error' => $e->getMessage(),
                'compte_id' => $compte->id
            ]);

            return back()->withErrors([
                'general' => 'Une erreur est survenue lors de la suppression.'
            ]);
        }
    }

    // Méthode de debug CORRIGÉE avec gestion d'erreur robuste
    public function debug()
    {
        try {
            // Vérifier l'authentification
            if (!Auth::check()) {
                return response()->json([
                    'error' => 'Non authentifié',
                    'authenticated' => false
                ], 401);
            }

            $debug = [
                'success' => true,
                'timestamp' => now()->toDateTimeString(),
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'user_authenticated' => Auth::check(),
                'table_exists' => false,
                'table_structure' => [],
                'fillable_fields' => [],
                'validation_rules' => [],
                'user_comptes' => [],
                'type_mapping' => [
                    'courant' => 'Compte Courant',
                    'epargne' => 'Compte Épargne',
                    'credit' => 'Compte Crédit',
                    'investissement' => 'Compte Investissement'
                ],
                'type_stats' => []
            ];

            // Vérifier si la table existe
            try {
                $debug['table_exists'] = Schema::hasTable('compte');
            } catch (\Exception $e) {
                $debug['table_error'] = $e->getMessage();
            }

            // Obtenir les champs fillable
            try {
                $compte = new Compte();
                $debug['fillable_fields'] = $compte->getFillable();
            } catch (\Exception $e) {
                $debug['fillable_error'] = $e->getMessage();
            }

            // Obtenir les règles de validation
            try {
                $debug['validation_rules'] = Compte::getValidationRules();
            } catch (\Exception $e) {
                $debug['validation_error'] = $e->getMessage();
            }

            if ($debug['table_exists']) {
                try {
                    // Structure de la table
                    $debug['table_structure'] = DB::select("DESCRIBE compte");
                } catch (\Exception $e) {
                    $debug['structure_error'] = $e->getMessage();
                }

                try {
                    // Récupérer les comptes avec détails
                    $comptes = Compte::where('user_id', Auth::id())
                                    ->orderBy('created_at', 'desc')
                                    ->get();
                    
                    $debug['user_comptes'] = $comptes->map(function($compte) {
                        $attributes = $compte->getAttributes();
                        return [
                            'id' => $compte->id,
                            'nom_compte' => $compte->nom_compte,
                            'type_compte' => $compte->type_compte,
                            'type_formatte' => $this->getTypeFormatte($compte->type_compte),
                            'solde' => $compte->solde,
                            'created_at' => $compte->created_at->toDateTimeString(),
                            'raw_attributes' => $attributes
                        ];
                    })->toArray();

                    // Statistiques des types
                    $debug['type_stats'] = $comptes->groupBy('type_compte')->map(function($group) {
                        return $group->count();
                    })->toArray();

                } catch (\Exception $e) {
                    $debug['comptes_error'] = $e->getMessage();
                }
            }
            
            // Forcer le header JSON
            return response()->json($debug, 200, [
                'Content-Type' => 'application/json'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur dans debug()', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? 'non_connecté'
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? null,
                'authenticated' => Auth::check()
            ], 500, [
                'Content-Type' => 'application/json'
            ]);
        }
    }

    // Méthode helper pour formater le type
    private function getTypeFormatte($type)
    {
        $types = [
            'courant' => 'Compte Courant',
            'epargne' => 'Compte Épargne',
            'credit' => 'Compte Crédit',
            'investissement' => 'Compte Investissement'
        ];

        return $types[$type] ?? ucfirst($type);
    }

    // Nouvelle méthode pour un debug simple
    public function debugSimple()
    {
        try {
            $user = Auth::user();
            $comptes = Compte::where('user_id', Auth::id())->get();

            $result = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ],
                'comptes' => $comptes->map(function($compte) {
                    return [
                        'id' => $compte->id,
                        'nom' => $compte->nom_compte,
                        'type' => $compte->type_compte,
                        'solde' => $compte->solde,
                        'created' => $compte->created_at->format('Y-m-d H:i:s')
                    ];
                })
            ];

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Méthode pour vérifier les routes
    public function checkRoutes()
    {
        $routes = [
            'comptes.index' => route('comptes.index'),
            'comptes.create' => route('comptes.create'),
            'comptes.store' => route('comptes.store'),
            'dashboard' => route('dashboard')
        ];

        return response()->json([
            'routes' => $routes,
            'current_url' => request()->url(),
            'user_authenticated' => Auth::check(),
            'user_id' => Auth::id()
        ]);
    }

    // Méthode pour corriger les types incorrects
    public function fixTypes()
    {
        try {
            $comptes = Compte::where('user_id', Auth::id())->get();
            $corrections = [];

            foreach ($comptes as $compte) {
                $typeOriginal = $compte->type_compte;
                
                // Vérifier si le type est valide
                if (!in_array($typeOriginal, ['courant', 'epargne', 'credit', 'investissement'])) {
                    // Essayer de corriger les types courants
                    $typeCorrige = $this->corrigerType($typeOriginal);
                    
                    if ($typeCorrige !== $typeOriginal) {
                        $compte->type_compte = $typeCorrige;
                        $compte->save();
                        
                        $corrections[] = [
                            'compte_id' => $compte->id,
                            'nom' => $compte->nom_compte,
                            'type_original' => $typeOriginal,
                            'type_corrige' => $typeCorrige
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'corrections' => $corrections,
                'message' => count($corrections) . ' compte(s) corrigé(s)'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function corrigerType($type)
    {
        $corrections = [
            'Compte Courant' => 'courant',
            'Compte Épargne' => 'epargne',
            'Compte Crédit' => 'credit',
            'Compte Investissement' => 'investissement',
            'épargne' => 'epargne', // au cas où
            'Épargne' => 'epargne',
            'Courant' => 'courant',
            'Crédit' => 'credit',
            'Investissement' => 'investissement'
        ];

        return $corrections[$type] ?? $type;
    }

    // Méthode pour tester la création avec gestion correcte du user_id
    public function testCreate()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Utilisateur non connecté'
                ], 401);
            }

            $testData = [
                'nom_compte' => 'Test Compte ' . now()->format('H:i:s'),
                'type_compte' => 'courant',
                'solde' => 500,
                'user_id' => Auth::id() // USER_ID AJOUTÉ EXPLICITEMENT
            ];

            if (Schema::hasColumn('compte', 'description')) {
                $testData['description'] = 'Compte créé automatiquement pour test';
            }

            Log::info('Test de création avec données', $testData);

            $compte = Compte::create($testData);

            return response()->json([
                'success' => true,
                'message' => 'Compte de test créé avec succès',
                'compte' => $compte,
                'user_id' => Auth::id()
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors du test de création', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'authenticated' => Auth::check()
            ], 500);
        }
    }
}
