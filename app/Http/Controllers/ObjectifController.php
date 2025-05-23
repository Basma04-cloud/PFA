<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Objectif;
use Illuminate\Support\Facades\Auth;

class ObjectifController extends Controller
{
    public function index()
    {
        $objectifs = Objectif::where('user_id', Auth::id())->get();
        return view('objectifs.index', compact('objectifs'));
    }

    public function create()
    {
        return view('objectifs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_objectif' => 'required|string|max:255',
            'montant_vise' => 'required|numeric|min:0.01',
            'montant_atteint' => 'nullable|numeric|min:0',
            'echeance' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        Objectif::create([
            'nom_objectif' => $request->nom_objectif,
            'montant_vise' => $request->montant_vise,
            'montant_atteint' => $request->montant_atteint ?? 0,
            'echeance' => $request->echeance,
            'description' => $request->description,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('objectifs.index')->with('success', 'Objectif créé avec succès!');
    }

    public function show(Objectif $objectif)
    {
        $this->authorize('view', $objectif);
        
        return view('objectifs.show', compact('objectif'));
    }

    public function edit(Objectif $objectif)
    {
        $this->authorize('update', $objectif);
        
        return view('objectifs.edit', compact('objectif'));
    }

    public function update(Request $request, Objectif $objectif)
    {
        $this->authorize('update', $objectif);
        
        $request->validate([
            'nom_objectif' => 'required|string|max:255',
            'montant_vise' => 'required|numeric|min:0.01',
            'montant_atteint' => 'nullable|numeric|min:0',
            'echeance' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $objectif->update([
            'nom_objectif' => $request->nom_objectif,
            'montant_vise' => $request->montant_vise,
            'montant_atteint' => $request->montant_atteint ?? $objectif->montant_atteint,
            'echeance' => $request->echeance,
            'description' => $request->description,
        ]);

        return redirect()->route('objectifs.index')->with('success', 'Objectif mis à jour avec succès!');
    }

    public function destroy(Objectif $objectif)
    {
        $this->authorize('delete', $objectif);
        
        $objectif->delete();

        return redirect()->route('objectifs.index')->with('success', 'Objectif supprimé avec succès!');
    }

    public function contribuer(Request $request, Objectif $objectif)
    {
        $this->authorize('update', $objectif);
        
        $request->validate([
            'montant' => 'required|numeric|min:0.01',
        ]);

        $objectif->montant_atteint += $request->montant;
        $objectif->save();

        return redirect()->route('objectifs.show', $objectif)->with('success', 'Contribution ajoutée avec succès!');
    }
}
