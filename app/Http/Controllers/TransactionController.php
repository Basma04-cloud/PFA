<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        // Données de test pour les transactions
        $transactions = [
            [
                'nom' => 'Courses',
                'date' => '15/01/2024',
                'montant' => -85.50,
                'categorie' => 'Alimentation',
                'icon' => 'shopping-cart'
            ],
            [
                'nom' => 'Salaire',
                'date' => '01/01/2024',
                'montant' => 2000.00,
                'categorie' => 'Revenus',
                'icon' => 'money-bag'
            ],
            [
                'nom' => 'Loyer',
                'date' => '01/01/2024',
                'montant' => -800.00,
                'categorie' => 'Logement',
                'icon' => 'house'
            ],
            [
                'nom' => 'Restaurant',
                'date' => '10/01/2024',
                'montant' => -45.00,
                'categorie' => 'Alimentation',
                'icon' => 'shopping-cart'
            ],
            [
                'nom' => 'Freelance',
                'date' => '12/01/2024',
                'montant' => 500.00,
                'categorie' => 'Revenus',
                'icon' => 'money-bag'
            ],
            [
                'nom' => 'Loisirs',
                'date' => '14/01/2024',
                'montant' => -25.00,
                'categorie' => 'Divertissement',
                'icon' => 'popcorn'
            ]
        ];

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        // Données de test pour les comptes
        $comptes = [
            (object) ['id' => 1, 'nom' => 'Compte Courant', 'solde' => 1245.50],
            (object) ['id' => 2, 'nom' => 'Livret A', 'solde' => 5000.00],
            (object) ['id' => 3, 'nom' => 'Carte de crédit', 'solde' => -350.00],
        ];

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
            'compte_id' => 'required|integer',
            'type' => 'required|in:depense,revenu,transfert',
            'description' => 'nullable|string|max:1000',
        ]);

        // Ici vous ajouteriez la logique pour sauvegarder en base de données
        // Transaction::create($validatedData);

        return redirect()->route('transactions.index')->with('success', 'Transaction créée avec succès !');
    }

    public function show($id)
    {
        // Logique pour afficher une transaction spécifique
        return view('transactions.show');
    }

    public function edit($id)
    {
        // Logique pour éditer une transaction
        return view('transactions.edit');
    }

    public function update(Request $request, $id)
    {
        // Logique pour mettre à jour une transaction
        return redirect()->route('transactions.index')->with('success', 'Transaction mise à jour !');
    }

    public function destroy($id)
    {
        // Logique pour supprimer une transaction
        return redirect()->route('transactions.index')->with('success', 'Transaction supprimée !');
    }
}
