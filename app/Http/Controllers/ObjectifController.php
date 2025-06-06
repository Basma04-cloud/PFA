<?php

namespace App\Http\Controllers;
use App\Models\Compte;
use App\Models\Objectif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ObjectifController extends Controller
{
    /**
     * Afficher la liste des objectifs
     */
    public function index(Request $request)
{
    try {
        $query = Objectif::where('user_id', Auth::id());

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Tri
        $tri = $request->get('tri', 'date_echeance');
        switch ($tri) {
            case 'progression':
                $query->orderByRaw('(montant_atteint / montant_vise) DESC');
                break;
            case 'montant_vise':
                $query->orderBy('montant_vise', 'desc');
                break;
            case 'created_at':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('date_echeance', 'asc');
        }

        $objectifs = $query->get();

        // Mettre à jour les statuts automatiquement
        foreach ($objectifs as $objectif) {
            $objectif->mettreAJourStatut();
        }

        // Statistiques
        $stats = [
            'total' => $objectifs->count(),
            'atteints' => $objectifs->where('statut', 'atteint')->count(),
            'actifs' => $objectifs->where('statut', 'actif')->count(),
            'montant_total_vise' => $objectifs->where('statut', '!=', 'atteint')->sum('montant_vise'),
            'montant_total_atteint' => $objectifs->sum('montant_atteint'),
        ];

        return view('objectifs.index', compact('objectifs', 'stats'));

    } catch (\Exception $e) {
        Log::error('Erreur lors du chargement des objectifs', [
            'error' => $e->getMessage(),
            'user_id' => Auth::id()
        ]);

        return view('objectifs.index', [
            'objectifs' => collect(),
            'stats' => [
                'total' => 0,
                'atteints' => 0,
                'actifs' => 0,
                'montant_total_vise' => 0,
                'montant_total_atteint' => 0
            ],
            'error' => 'Erreur lors du chargement des objectifs'
        ]);
    }
}


    /**
     * Afficher le formulaire de création
     */
    
public function create()
{
    $comptes = Compte::where('user_id', auth()->id())->get();
    return view('objectifs.create', compact('comptes'));
}


    /**
     * Enregistrer un nouvel objectif
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
             'nom' => 'required|string|max:255',
    'montant_vise' => 'required|numeric|min:0.01',
    'date_echeance' => 'required|date|after:today',
    'montant_initial' => 'nullable|numeric|min:0', // ← CORRIGÉ
    'compte_id' => 'required|exists:compte,id',
    'description' => 'nullable|string'
        ]);
        

        try {
            $objectifData = [
                'nom' => $validatedData['nom'],
                'description' => $validatedData['description'],
                'montant_vise' => $validatedData['montant_vise'],
                'date_echeance' => $validatedData['date_echeance'],
                'user_id' => Auth::id(),
                'montant_atteint' => $validatedData['montant_initial'] ?? 0,
                'statut' => 'actif',
                'compte_id' => $validatedData['compte_id'],

                ];

            $objectif = Objectif::create($objectifData);
            
            // Vérifier immédiatement si l'objectif est atteint
            $objectif->mettreAJourStatut();

            return redirect()->route('objectifs.index')->with('success', 'Objectif créé avec succès !');

        } catch (\Exception $e) {
            dd([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'data' => $validatedData
    ]);
            Log::error('Erreur lors de la création de l\'objectif', [
                'error' => $e->getMessage(),
                'data' => $validatedData,
                'user_id' => Auth::id()
            ]);

            return back()->withErrors([
                'general' => 'Une erreur est survenue lors de la création de l\'objectif.'
            ])->withInput();
        }
    }

    /**
     * Afficher les détails d'un objectif
     */
    public function show($id)
    {
        try {
            $objectif = Objectif::where('user_id', Auth::id())
                            ->where('id', $id)
                            ->firstOrFail();

            // Mettre à jour le statut
            $objectif->mettreAJourStatut();

            return view('objectifs.show', compact('objectif'));

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage de l\'objectif', [
                'error' => $e->getMessage(),
                'objectif_id' => $id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('objectifs.index')
                           ->withErrors(['general' => 'Objectif non trouvé.']);
        }
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit($id)
    {
        try {
            $objectif = Objectif::where('user_id', Auth::id())
                               ->where('id', $id)
                               ->firstOrFail();

            return view('objectifs.edit', compact('objectif'));

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage du formulaire de modification', [
                'error' => $e->getMessage(),
                'objectif_id' => $id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('objectifs.index')
                           ->withErrors(['general' => 'Objectif non trouvé.']);
        }
    }

    /**
     * Mettre à jour un objectif
     */
    public function update(Request $request, $id)
    {
        try {
            $objectif = Objectif::where('user_id', Auth::id())
                               ->where('id', $id)
                               ->firstOrFail();

            // Validation différente selon l'action
            if ($request->has('statut')) {
                // Changement de statut simple
                $validatedData = $request->validate([
                    'statut' => 'required|in:actif,atteint,abandonne'
                ]);

                $objectif->update(['statut' => $validatedData['statut']]);
                $message = 'Statut de l\'objectif mis à jour avec succès.';

            } else {
                // Modification complète
                $validatedData = $request->validate([
                    'nom' => 'required|string|max:255',
                    'description' => 'nullable|string|max:1000',
                    'montant_vise' => 'required|numeric|min:0.01|max:999999.99',
                    'date_echeance' => 'required|date',
                    'montant_atteint' => 'nullable|numeric|min:0|max:999999.99',
                ]);

                // Vérifier que le montant atteint n'est pas supérieur au montant visé (sauf si déjà dépassé)
                if (isset($validatedData['montant_atteint']) && 
                    $validatedData['montant_atteint'] > $validatedData['montant_vise'] && 
                    $objectif->montant_atteint <= $objectif->montant_vise) {
                    
                    return back()->withErrors([
                        'montant_atteint' => 'Le montant atteint ne peut pas être supérieur au montant visé.'
                    ])->withInput();
                }

                $objectif->update([
                    'nom' => $validatedData['nom'],
                    'description' => $validatedData['description'],
                    'montant_vise' => $validatedData['montant_vise'],
                    'date_echeance' => $validatedData['date_echeance'],
                    'montant_atteint' => $validatedData['montant_atteint'] ?? $objectif->montant_atteint,
                ]);

                $message = 'Objectif mis à jour avec succès.';
            }

            // Mettre à jour le statut automatiquement
            $objectif->mettreAJourStatut();

            return redirect()->route('objectifs.index')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'objectif', [
                'error' => $e->getMessage(),
                'objectif_id' => $id,
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            return back()->withErrors([
                'general' => 'Une erreur est survenue lors de la mise à jour de l\'objectif.'
            ])->withInput();
        }
    }

    /**
     * Supprimer un objectif
     */
    public function destroy($id)
    {
        try {
            $objectif = Objectif::where('user_id', Auth::id())
                               ->where('id', $id)
                               ->firstOrFail();

            $nomObjectif = $objectif->nom;
            $objectif->delete();

            return redirect()->route('objectifs.index')
                           ->with('success', "Objectif '{$nomObjectif}' supprimé avec succès.");

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'objectif', [
                'error' => $e->getMessage(),
                'objectif_id' => $id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('objectifs.index')
                           ->withErrors(['general' => 'Une erreur est survenue lors de la suppression de l\'objectif.']);
        }
    }

    /**
     * Ajouter une contribution à un objectif
     */
    public function contribuer(Request $request, $id)
    {
        $validatedData = $request->validate([
            'montant' => 'required|numeric|min:0.01|max:999999.99',
        ]);

        try {
            $objectif = Objectif::where('user_id', Auth::id())
                               ->where('id', $id)
                               ->firstOrFail();

            DB::transaction(function () use ($objectif, $validatedData) {
                $objectif->ajouterContribution($validatedData['montant']);
            });

            $message = 'Contribution ajoutée avec succès !';
            if ($objectif->fresh()->is_atteint) {
                $message .= ' Félicitations, votre objectif est atteint !';
            }

            return redirect()->route('objectifs.index')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout de contribution', [
                'error' => $e->getMessage(),
                'objectif_id' => $id,
                'user_id' => Auth::id()
            ]);

            return back()->withErrors([
                'general' => 'Une erreur est survenue lors de l\'ajout de la contribution.'
            ])->withInput();
        }
    }

    /**
     * Corriger le statut d'un objectif
     */
    public function corriger($id)
    {
        try {
            $objectif = Objectif::where('user_id', Auth::id())
                               ->where('id', $id)
                               ->firstOrFail();

            // Forcer la mise à jour du statut
            $ancienStatut = $objectif->statut;
            $objectif->mettreAJourStatut();
            $nouveauStatut = $objectif->fresh()->statut;

            if ($ancienStatut !== $nouveauStatut) {
                $message = "Statut corrigé : '{$ancienStatut}' → '{$nouveauStatut}'";
            } else {
                $message = 'Le statut était déjà correct.';
            }

            return redirect()->route('objectifs.index')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la correction du statut', [
                'error' => $e->getMessage(),
                'objectif_id' => $id,
                'user_id' => Auth::id()
            ]);

            return back()->withErrors(['general' => 'Erreur lors de la correction.']);
        }
    }

    /**
     * Corriger tous les objectifs d'un utilisateur
     */
    public function corrigerTous()
    {
        try {
            $objectifs = Objectif::where('user_id', Auth::id())->get();
            $corriges = 0;

            foreach ($objectifs as $objectif) {
                $ancienStatut = $objectif->statut;
                if ($objectif->mettreAJourStatut()) {
                    $corriges++;
                }
            }

            $message = $corriges > 0 
                ? "{$corriges} objectif(s) corrigé(s) !" 
                : "Tous les statuts étaient déjà corrects.";

            return redirect()->route('objectifs.index')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la correction de tous les objectifs', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->withErrors(['general' => 'Erreur lors de la correction.']);
        }
    }

    /**
     * Méthode de debug
     */
    public function debug()
    {
        try {
            $user_id = Auth::id();
            $objectifs = Objectif::where('user_id', $user_id)->get();
            
            $debug_info = [
                'user_id' => $user_id,
                'objectifs_count' => $objectifs->count(),
                'objectifs_data' => $objectifs->map(function($obj) {
                    return [
                        'id' => $obj->id,
                        'nom' => $obj->nom,
                        'montant_vise' => $obj->montant_vise,
                        'montant_atteint' => $obj->montant_atteint,
                        'pourcentage' => $obj->pourcentage,
                        'statut' => $obj->statut,
                        'is_atteint' => $obj->is_atteint,
                        'date_echeance' => $obj->date_echeance->format('Y-m-d'),
                    ];
                }),
                'table_structure' => \DB::select("DESCRIBE objectifs"),
            ];
            
            return response()->json($debug_info);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
