<?php

namespace App\Http\Controllers;

use App\Models\Objectif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ObjectifController extends Controller
{
    public function index()
    {
        // Récupérer les objectifs de l'utilisateur connecté
        $objectifs = Objectif::where('user_id', Auth::id())
                            ->orderBy('date_echeance', 'asc')
                            ->get();

        return view('objectifs.index', compact('objectifs'));
    }

    public function create()
    {
        return view('objectifs.create');
    }

    public function store(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'montant_vise' => 'required|numeric|min:0.01',
            'date_echeance' => 'required|date|after:today',
            'montant_initial' => 'nullable|numeric|min:0',
        ]);

        // Créer l'objectif
        Objectif::create([
            'nom' => $validatedData['nom'],
            'description' => $validatedData['description'],
            'montant_vise' => $validatedData['montant_vise'],
            'montant_atteint' => $validatedData['montant_initial'] ?? 0,
            'date_echeance' => $validatedData['date_echeance'],
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('objectifs.index')->with('success', 'Objectif créé avec succès !');
    }

    public function show($id)
    {
        // Récupérer l'objectif de l'utilisateur connecté
        $objectif = Objectif::where('user_id', Auth::id())
                           ->where('id', $id)
                           ->firstOrFail();

        return view('objectifs.show', compact('objectif'));
    }

    public function edit($id)
    {
        // Récupérer l'objectif de l'utilisateur connecté
        $objectif = Objectif::where('user_id', Auth::id())
                           ->where('id', $id)
                           ->firstOrFail();

        return view('objectifs.edit', compact('objectif'));
    }

    public function update(Request $request, $id)
    {
        // Validation des données
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'montant_vise' => 'required|numeric|min:0.01',
            'montant_atteint' => 'required|numeric|min:0',
            'date_echeance' => 'required|date',
            'statut' => 'required|in:actif,atteint,abandonne',
        ]);

        // Récupérer et mettre à jour l'objectif
        $objectif = Objectif::where('user_id', Auth::id())
                           ->where('id', $id)
                           ->firstOrFail();

        $objectif->update($validatedData);

        return redirect()->route('objectifs.index')->with('success', 'Objectif mis à jour avec succès !');
    }

    public function destroy($id)
    {
        // Récupérer et supprimer l'objectif
        $objectif = Objectif::where('user_id', Auth::id())
                           ->where('id', $id)
                           ->firstOrFail();

        $objectif->delete();

        return redirect()->route('objectifs.index')->with('success', 'Objectif supprimé avec succès !');
    }

    public function contribuer(Request $request, $id)
    {
        // Validation du montant
        $validatedData = $request->validate([
            'montant' => 'required|numeric|min:0.01',
        ]);

        // Récupérer l'objectif
        $objectif = Objectif::where('user_id', Auth::id())
                           ->where('id', $id)
                           ->firstOrFail();

        // Ajouter la contribution
        $objectif->montant_atteint += $validatedData['montant'];
        
        // Vérifier si l'objectif est atteint
        if ($objectif->montant_atteint >= $objectif->montant_vise) {
            $objectif->statut = 'atteint';
        }
        
        $objectif->save();

        return redirect()->route('objectifs.index')->with('success', 'Contribution ajoutée avec succès !');
    }
}
