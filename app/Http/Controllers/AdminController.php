<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Objectif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;




class AdminController extends Controller
{

    public function dashboard()
    {
        // Statistiques globales
        $totalUsers = User::count();
        $totalRevenues = Transaction::where('montant', '>', 0)->sum('montant');
        $totalExpenses = abs(Transaction::where('montant', '<', 0)->sum('montant'));
        
        // Utilisateurs récents
        $recentUsers = User::latest()->limit(5)->get();
        
        // Objectifs populaires
        $popularObjectives = DB::table('objectifs')
            ->select('nom as name', DB::raw('COUNT(*) as users_count'))
            ->groupBy('nom')
            ->orderByDesc('users_count')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalRevenues', 
            'totalExpenses',
            'recentUsers',
            'popularObjectives'
        ));
    }

    public function users()
    {
        $users = User::with(['comptes', 'transactions'])
            ->paginate(20);
            
        return view('admin.users', compact('users'));
    }

    public function showUser(User $user)
    {
        $user->load(['comptes', 'transactions', 'objectifs']);
        
        return view('admin.users', compact('user'));
    }

    public function statistics()
    {
        // Logique pour les statistiques détaillées
        return view('admin.statistics');
    }
    


    

}