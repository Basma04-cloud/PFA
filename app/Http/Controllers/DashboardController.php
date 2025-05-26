<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Compte;
use App\Models\Objectif;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Données de test pour le dashboard
        $soldeTotal = 5895.50; // Somme de tous les comptes
        
        // Calcul des dépenses du mois (transactions négatives)
        $depensesDuMois = 955.50;
        
        // Calcul des revenus du mois (transactions positives)
        $revenusDuMois = 2500.00;
        
        // Dernières transactions (données de test)
        $dernieresTransactions = [
            (object) [
                'nom' => 'Salaire',
                'date' => '01/01/2024',
                'montant' => 2000.00,
                'categorie' => 'Revenus'
            ],
            (object) [
                'nom' => 'Loyer',
                'date' => '01/01/2024',
                'montant' => -800.00,
                'categorie' => 'Logement'
            ],
            (object) [
                'nom' => 'Courses',
                'date' => '02/01/2024',
                'montant' => -120.50,
                'categorie' => 'Alimentation'
            ],
            (object) [
                'nom' => 'Freelance',
                'date' => '05/01/2024',
                'montant' => 500.00,
                'categorie' => 'Revenus'
            ],
            (object) [
                'nom' => 'Restaurant',
                'date' => '10/01/2024',
                'montant' => -35.00,
                'categorie' => 'Alimentation'
            ]
        ];

        return view('dashboard', compact(
            'soldeTotal',
            'depensesDuMois', 
            'revenusDuMois',
            'dernieresTransactions'
        ));
    }

    // Méthode pour récupérer les vraies données (à utiliser plus tard)
    private function getRealData()
    {
        $user = Auth::user();
        
        // Calcul du solde total
        $soldeTotal = Compte::where('user_id', $user->id)->sum('solde');
        
        // Calcul des dépenses du mois
        $depensesDuMois = abs(Transaction::where('user_id', $user->id)
            ->moisCourant()
            ->depenses()
            ->sum('montant'));
        
        // Calcul des revenus du mois
        $revenusDuMois = Transaction::where('user_id', $user->id)
            ->moisCourant()
            ->revenus()
            ->sum('montant');
        
        // Dernières transactions
        $dernieresTransactions = Transaction::where('user_id', $user->id)
            ->with('compte')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        return compact('soldeTotal', 'depensesDuMois', 'revenusDuMois', 'dernieresTransactions');
    }
}
