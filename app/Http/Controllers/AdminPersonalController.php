<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPersonalController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Données personnelles de l'admin (même logique que le dashboard utilisateur)
        $comptes = $user->comptes;
        $soldeTotal = $comptes->sum('solde');
        $nombreComptes = $comptes->count();
        
        // Transactions du mois en cours
        $debutMois = now()->startOfMonth();
        $finMois = now()->endOfMonth();
        
        $transactionsDuMois = $user->transactions()
            ->whereBetween('date', [$debutMois, $finMois])
            ->get();
            
        $depensesDuMois = $transactionsDuMois->where('montant', '<', 0)->sum('montant');
        $revenusDuMois = $transactionsDuMois->where('montant', '>', 0)->sum('montant');
        
        // Objectifs personnels
        $personalObjectives = $user->objectifs()->get();
        
        // Transactions récentes
        $recentPersonalTransactions = $user->transactions()
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        $personalData = [
            'soldeTotal' => $soldeTotal,
            'nombreComptes' => $nombreComptes,
            'depensesDuMois' => abs($depensesDuMois),
            'revenusDuMois' => $revenusDuMois,
        ];

        return view('admin.dashboard', compact(
            'personalData',
            'personalObjectives',
            'recentPersonalTransactions'
        ));
    }

    public function comptes()
    {
        $comptes = Auth::user()->comptes;
        return view('admin.personal.comptes', compact('comptes'));
    }

    public function transactions()
    {
        $transactions = Auth::user()->transactions()
            ->
    }
}