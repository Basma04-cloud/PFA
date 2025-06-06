<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        return view('admin.statistics');
    }

    public function getData()
    {
        // Utilisateurs créés par mois (sur les 6 derniers mois)
        $monthlyUsers = User::select(
                DB::raw("DATE_FORMAT(created_at, '%b %Y') as date"),
                DB::raw("COUNT(*) as users")
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('date')
            ->orderByRaw("MIN(created_at)")
            ->get();

        // Revenus vs Dépenses par mois
        $revenusDepenses = DB::table('transactions')
            ->selectRaw("DATE_FORMAT(date, '%b %Y') as mois, type, SUM(montant) as total")
            ->whereIn('type', ['revenu', 'depense'])
            ->where('date', '>=', now()->subMonths(6))
            ->groupBy('mois', 'type')
            ->orderByRaw("MIN(date)")
            ->get();

        // Organiser les données
        $mois = [];
        $revenus = [];
        $depenses = [];

        foreach ($revenusDepenses as $item) {
            if (!in_array($item->mois, $mois)) {
                $mois[] = $item->mois;
            }

            if ($item->type === 'revenu') {
                $revenus[$item->mois] = $item->total;
            } else {
                $depenses[$item->mois] = $item->total;
            }
        }

        $revenusData = [];
        $depensesData = [];
        foreach ($mois as $m) {
            $revenusData[] = $revenus[$m] ?? 0;
            $depensesData[] = $depenses[$m] ?? 0;
        }
        $objectifs = DB::table('objectifs')
    ->select('statut', DB::raw('COUNT(*) as total'))
    ->groupBy('statut')
    ->get();

// Préparer les données pour le graphique
$labels = [];
$counts = [];

foreach ($objectifs as $obj) {
    $labels[] = ucfirst($obj->statut); // Pour avoir "Actif", "Atteint", etc.
    $counts[] = $obj->total;
}

        return response()->json([
            'monthly_users' => $monthlyUsers,
            'monthly_revenue_expense' => [
                'labels' => $mois,
                'revenus' => $revenusData,
                'depenses' => $depensesData,
            ],
            'objective_distribution' => collect($labels)->map(function ($label, $i) use ($counts) {
    return [
        'objective' => $label,
        'count' => $counts[$i],
    ];
}),

        ]);
    }
}




