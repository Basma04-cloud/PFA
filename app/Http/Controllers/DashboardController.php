<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Compte;
use App\Models\Categorie;
use App\Models\Budget;
use App\Models\Objectif;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Calcul du solde total
        $soldeTotal = Compte::where('user_id', $user->id)->sum('solde');

        // Calcul des dépenses du mois
        $depensesDuMois = Transaction::where('user_id', $user->id)
            ->where('type', 'dépense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('montant');

        // Calcul des revenus du mois
        $revenusDuMois = Transaction::where('user_id', $user->id)
            ->where('type', 'revenu')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('montant');

        // Dernières transactions
        $dernieresTransactions = Transaction::where('user_id', $user->id)
            ->with(['categorie', 'compte'])
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Données pour le graphique (revenus et dépenses par mois)
        $graphiqueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenus = Transaction::where('user_id', $user->id)
                ->where('type', 'revenu')
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('montant');
            
            $depenses = Transaction::where('user_id', $user->id)
                ->where('type', 'dépense')
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('montant');

            $graphiqueData[] = [
                'mois' => $date->format('M Y'),
                'revenus' => $revenus,
                'depenses' => $depenses
            ];
        }

        return view('dashboard', compact(
            'user',
            'soldeTotal',
            'depensesDuMois',
            'revenusDuMois',
            'dernieresTransactions',
            'graphiqueData'
        ));
    }
}