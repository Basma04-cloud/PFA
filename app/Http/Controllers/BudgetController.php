<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Categorie;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::where('user_id', Auth::id())
            ->with('categorie')
            ->get();
            
        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        $categories = Categorie::where('type', 'dépense')->get();
        return view('budgets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'montant_limite' => 'required|numeric|min:0.01',
            'periode' => 'required|in:mensuel,hebdomadaire,annuel',
            'categorie_id' => 'required|exists:categorie,id',
        ]);

        // Vérifier si un budget existe déjà pour cette catégorie et cette période
        $budgetExistant = Budget::where('user_id', Auth::id())
            ->where('categorie_id', $request->categorie_id)
            ->where('periode', $request->periode)
            ->first();

        if ($budgetExistant) {
            return redirect()->route('budgets.create')->with('error', 'Un budget existe déjà pour cette catégorie et cette période.');
        }

        Budget::create([
            'montant_limite' => $request->montant_limite,
            'periode' => $request->periode,
            'categorie_id' => $request->categorie_id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('budgets.index')->with('success', 'Budget créé avec succès!');
    }

    public function edit(Budget $budget)
    {
        $this->authorize('update', $budget);
        
        $categories = Categorie::where('type', 'dépense')->get();
        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);
        
        $request->validate([
            'montant_limite' => 'required|numeric|min:0.01',
            'periode' => 'required|in:mensuel,hebdomadaire,annuel',
            'categorie_id' => 'required|exists:categorie,id',
        ]);

        // Vérifier si un budget existe déjà pour cette catégorie et cette période (sauf le budget actuel)
        $budgetExistant = Budget::where('user_id', Auth::id())
            ->where('categorie_id', $request->categorie_id)
            ->where('periode', $request->periode)
            ->where('id', '!=', $budget->id)
            ->first();

        if ($budgetExistant) {
            return redirect()->route('budgets.edit', $budget)->with('error', 'Un budget existe déjà pour cette catégorie et cette période.');
        }

        $budget->update([
            'montant_limite' => $request->montant_limite,
            'periode' => $request->periode,
            'categorie_id' => $request->categorie_id,
        ]);

        return redirect()->route('budgets.index')->with('success', 'Budget mis à jour avec succès!');
    }

    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        
        $budget->delete();

        return redirect()->route('budgets.index')->with('success', 'Budget supprimé avec succès!');
    }
}