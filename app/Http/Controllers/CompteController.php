<?php

namespace App\Http\Controllers;

use App\Models\Compte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompteController extends Controller
{
    // Affiche tous les comptes de l'utilisateur connecté
    public function index()
    {
        $comptes = Compte::where('user_id', Auth::id())
                         ->orderBy('created_at', 'desc')
                         ->get();

        return view('comptes.index', compact('comptes'));
    }

    // Affiche le formulaire de création
    public function create()
    {
        return view('comptes.create');
    }

    // Enregistre un nouveau compte
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:courant,epargne,credit,investissement',
            'solde' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        Compte::create([
            'nom' => $validatedData['nom'],
            'type' => $validatedData['type'],
            'solde' => $validatedData['solde'] ?? 0,
            'description' => $validatedData['description'] ?? null,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('comptes.index')->with('success', 'Compte créé avec succès.');
    }

    // Affiche le formulaire de modification
    public function edit(Compte $compte)
    {
        // Vérifie que le compte appartient à l'utilisateur
        if ($compte->user_id !== Auth::id()) {
            abort(403);
        }

        return view('comptes.edit', compact('compte'));
    }

    // Met à jour un compte
    public function update(Request $request, Compte $compte)
    {
        if ($compte->user_id !== Auth::id()) {
            abort(403);
        }

        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:courant,epargne,credit,investissement',
            'solde' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $compte->update($validatedData);

        return redirect()->route('comptes.index')->with('success', 'Compte mis à jour avec succès.');
    }

    // Supprime un compte
    public function destroy(Compte $compte)
    {
        if ($compte->user_id !== Auth::id()) {
            abort(403);
        }

        $compte->delete();

        return redirect()->route('comptes.index')->with('success', 'Compte supprimé.');
    }
}
