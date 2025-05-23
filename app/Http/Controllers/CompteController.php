<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compte;
use Illuminate\Support\Facades\Auth;

class CompteController extends Controller
{
    public function index()
    {
        $comptes = Compte::where('user_id', Auth::id())->get();
        return view('comptes.index', compact('comptes'));
    }

    public function create()
    {
        return view('comptes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_compte' => 'required|string|max:255',
            'type' => 'required|in:Epargne,Espèces,Bancaire,Crédit',
            'solde' => 'required|numeric',
        ]);

        Compte::create([
            'nom_compte' => $request->nom_compte,
            'type' => $request->type,
            'solde' => $request->solde,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('comptes.index')->with('success', 'Compte créé avec succès!');
    }

    public function show(Compte $compte)
    {
        $this->authorize('view', $compte);
        
        $transactions = $compte->transactions()->with('categorie')->orderBy('date', 'desc')->paginate(10);
        
        return view('comptes.show', compact('compte', 'transactions'));
    }

    public function edit(Compte $compte)
    {
        $this->authorize('update', $compte);
        
        return view('comptes.edit', compact('compte'));
    }

    public function update(Request $request, Compte $compte)
    {
        $this->authorize('update', $compte);
        
        $request->validate([
            'nom_compte' => 'required|string|max:255',
            'type' => 'required|in:Epargne,Espèces,Bancaire,Crédit',
            'solde' => 'required|numeric',
        ]);

        $compte->update([
            'nom_compte' => $request->nom_compte,
            'type' => $request->type,
            'solde' => $request->solde,
        ]);

        return redirect()->route('comptes.index')->with('success', 'Compte mis à jour avec succès!');
    }

    public function destroy(Compte $compte)
    {
        $this->authorize('delete', $compte);
        
        $compte->delete();

        return redirect()->route('comptes.index')->with('success', 'Compte supprimé avec succès!');
    }
}
