<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categorie;

class CategorieController extends Controller
{
    public function index()
    {
        $categories = Categorie::all();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_categorie' => 'required|string|max:255',
            'type' => 'required|in:revenu,dépense',
        ]);

        Categorie::create([
            'nom_categorie' => $request->nom_categorie,
            'type' => $request->type,
        ]);

        return redirect()->route('categories.index')->with('success', 'Catégorie créée avec succès!');
    }

    public function edit(Categorie $categorie)
    {
        return view('categories.edit', compact('categorie'));
    }

    public function update(Request $request, Categorie $categorie)
    {
        $request->validate([
            'nom_categorie' => 'required|string|max:255',
            'type' => 'required|in:revenu,dépense',
        ]);

        $categorie->update([
            'nom_categorie' => $request->nom_categorie,
            'type' => $request->type,
        ]);

        return redirect()->route('categories.index')->with('success', 'Catégorie mise à jour avec succès!');
    }

    public function destroy(Categorie $categorie)
    {
        // Vérifier si la catégorie est utilisée dans des transactions ou des budgets
        if ($categorie->transactions()->count() > 0 || $categorie->budgets()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Impossible de supprimer cette catégorie car elle est utilisée dans des transactions ou des budgets.');
        }

        $categorie->delete();

        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée avec succès!');
    }
}