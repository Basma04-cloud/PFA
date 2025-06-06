<?php
// Script de debug pour vérifier le flux de données du dashboard

echo "🔍 DEBUG DU FLUX DE DONNÉES DASHBOARD\n";
echo "=====================================\n\n";

// Simuler un utilisateur connecté (remplacez par votre ID utilisateur)
$userId = 1; // Changez ceci par votre ID utilisateur

try {
    // Test 1: Vérifier les transactions
    echo "📊 TEST 1: Transactions\n";
    echo "-----------------------\n";
    
    $transactions = \App\Models\Transaction::where('user_id', $userId)->get();
    echo "Nombre de transactions: " . $transactions->count() . "\n";
    
    if ($transactions->count() > 0) {
        $revenus = $transactions->where('montant', '>', 0)->sum('montant');
        $depenses = abs($transactions->where('montant', '<', 0)->sum('montant'));
        echo "Total revenus: " . number_format($revenus, 2) . " €\n";
        echo "Total dépenses: " . number_format($depenses, 2) . " €\n";
        
        // Dépenses par catégorie
        $categories = $transactions->where('montant', '<', 0)->groupBy('categorie');
        echo "Catégories de dépenses: " . $categories->keys()->implode(', ') . "\n";
    }
    
    echo "\n";
    
    // Test 2: Vérifier les comptes
    echo "🏦 TEST 2: Comptes\n";
    echo "------------------\n";
    
    $comptes = \App\Models\Compte::where('user_id', $userId)->get();
    echo "Nombre de comptes: " . $comptes->count() . "\n";
    
    if ($comptes->count() > 0) {
        foreach ($comptes as $compte) {
            echo "- " . $compte->nom_compte . ": " . number_format($compte->solde, 2) . " €\n";
        }
        $soldeTotal = $comptes->sum('solde');
        echo "Solde total: " . number_format($soldeTotal, 2) . " €\n";
    }
    
    echo "\n";
    
    // Test 3: Vérifier les objectifs
    echo "🎯 TEST 3: Objectifs\n";
    echo "--------------------\n";
    
    $objectifs = \App\Models\Objectif::where('user_id', $userId)->get();
    echo "Nombre d'objectifs: " . $objectifs->count() . "\n";
    
    if ($objectifs->count() > 0) {
        foreach ($objectifs as $objectif) {
            $progression = $objectif->montant_vise > 0 
                ? ($objectif->montant_atteint / $objectif->montant_vise) * 100 
                : 0;
            echo "- " . $objectif->nom . ": " . round($progression, 1) . "% (" . $objectif->statut . ")\n";
        }
    }
    
    echo "\n";
    
    // Test 4: Simuler les APIs
    echo "🔗 TEST 4: Simulation des APIs\n";
    echo "-------------------------------\n";
    
    // Données mensuelles
    $monthsData = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        
        $revenus = \App\Models\Transaction::where('user_id', $userId)
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->where('montant', '>', 0)
            ->sum('montant');
        
        $depenses = abs(\App\Models\Transaction::where('user_id', $userId)
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->where('montant', '<', 0)
            ->sum('montant'));
        
        $monthsData[] = [
            'mois' => $date->format('M Y'),
            'revenus' => (float) $revenus,
            'depenses' => (float) $depenses
        ];
    }
    
    echo "Données mensuelles:\n";
    foreach ($monthsData as $month) {
        echo "  " . $month['mois'] . ": " . $month['revenus'] . "€ / " . $month['depenses'] . "€\n";
    }
    
    echo "\n✅ Tests terminés !\n";
    echo "\n💡 Si vous voyez des données ici, les graphiques devraient fonctionner.\n";
    echo "💡 Si tout est à 0, ajoutez quelques transactions de test.\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
