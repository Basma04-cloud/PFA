@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="flex h-screen bg-purple-400">
    <!-- Sidebar -->
    <div class="w-64 bg-purple-800 text-white p-6 flex flex-col">
        <h1 class="text-3xl font-bold mb-10">Dashboard</h1>
        <nav class="space-y-6 flex-1">
            <a href="{{ route('dashboard') }}" class="block text-xl font-semibold text-purple-200">Dashboard</a>
            <a href="{{ route('comptes.index') }}" class="block text-xl hover:text-gray-300 transition-colors">Comptes</a>
            <a href="{{ route('transactions.index') }}" class="block text-xl hover:text-gray-300 transition-colors">Transactions</a>
            <a href="{{ route('objectifs.index') }}" class="block text-xl hover:text-gray-300 transition-colors">Objectifs</a>
            <a href="{{ route('notifications.index') }}" class="block text-xl hover:text-gray-300 transition-colors">Notifications</a>
            <a href="{{ route('profil.index') }}" class="block text-xl hover:text-gray-300 transition-colors">Profil</a>
        </nav>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="block text-xl mt-auto hover:text-gray-300">Déconnexion</button>
        </form>
    </div>

    <!-- Main content -->
    <div class="flex-1 bg-purple-400 p-8 overflow-auto">
        <header class="flex justify-between items-center mb-8">
            <h2 class="text-2xl text-white">Tableau de bord</h2>
            <div class="text-white text-xl">Bienvenue{{ Auth::check() ? ', ' . Auth::user()->name : '' }}</div>
        </header>

        @if(isset($error))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ $error }}
            <button onclick="testAPIs()" class="ml-4 bg-red-600 text-white px-3 py-1 rounded text-sm">
                Tester les APIs
            </button>
        </div>
        @endif

        <!-- Indicateur de chargement -->
        <div id="loadingIndicator" class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4" style="display: none;">
            🔄 Chargement des graphiques en cours...
        </div>

        <!-- Indicateur d'erreur -->
        <div id="errorIndicator" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" style="display: none;">
            ❌ Erreur de chargement des graphiques. 
            <button onclick="retryLoadCharts()" class="ml-2 bg-red-600 text-white px-3 py-1 rounded text-sm">
                Réessayer
            </button>
            <button onclick="testAPIs()" class="ml-2 bg-blue-600 text-white px-3 py-1 rounded text-sm">
                Diagnostiquer
            </button>
        </div>

        <!-- Cards principales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-300 shadow-none rounded-lg p-6">
                <h3 class="text-center text-black font-semibold mb-4">Solde total</h3>
                <p class="text-center text-2xl font-bold text-black">
                    {{ number_format($soldeTotal ?? 0, 2, ',', ' ') }} €
                </p>
                <p class="text-center text-sm text-gray-600 mt-2">
                    {{ $nombreComptes ?? 0 }} compte(s)
                </p>
            </div>

            <div class="bg-gray-300 shadow-none rounded-lg p-6">
                <h3 class="text-center text-black font-semibold mb-4">Dépenses du mois</h3>
                <p class="text-center text-2xl font-bold text-red-600">
                    {{ number_format($depensesDuMois ?? 0, 2, ',', ' ') }} €
                </p>
                <p class="text-center text-sm text-gray-600 mt-2">
                    {{ now()->format('F Y') }}
                </p>
            </div>

            <div class="bg-gray-300 shadow-none rounded-lg p-6">
                <h3 class="text-center text-black font-semibold mb-4">Revenus du mois</h3>
                <p class="text-center text-2xl font-bold text-green-600">
                    {{ number_format($revenusDuMois ?? 0, 2, ',', ' ') }} €
                </p>
                <p class="text-center text-sm text-gray-600 mt-2">
                    {{ now()->format('F Y') }}
                </p>
            </div>
        </div>

        <!-- Graphiques section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Graphique 1: Évolution mensuelle 
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <h3 class="text-xl font-bold text-gray-800 mb-4">📈 Évolution mensuelle</h3>
                <div class="h-64 relative">
                    <canvas id="monthlyChart"></canvas>
                    <div id="monthlyLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75">
                        <div class="text-gray-500">Chargement...</div>
                    </div>
                </div> -->
            </div> 

            <!-- Graphique 2: Répartition des dépenses 
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <h3 class="text-xl font-bold text-gray-800 mb-4">🍰 Répartition des dépenses</h3>
                <div class="h-64 relative">
                    <canvas id="expensesChart"></canvas>
                    <div id="expensesLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75">
                        <div class="text-gray-500">Chargement...</div>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Deuxième ligne de graphiques -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Graphique 3: Objectifs -->
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <h3 class="text-xl font-bold text-gray-800 mb-4">🎯 Progression des objectifs</h3>
                <div class="h-64 relative">
                    <canvas id="objectifsChart"></canvas>
                    <div id="objectifsLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75">
                        <div class="text-gray-500">Chargement...</div>
                    </div>
                </div>
            </div>

            <!-- Graphique 4: Solde des comptes 
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <h3 class="text-xl font-bold text-gray-800 mb-4">💰 Solde des comptes</h3>
                <div class="h-64 relative">
                    
                    <div id="comptesLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75">
                        <div class="text-gray-500">Chargement...</div>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Graphique large: Tendance annuelle 
        <div class="bg-white rounded-lg p-6 shadow-lg mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">📊 Tendance annuelle</h3>
            <div class="h-80 relative">
                <div id="yearlyLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75">
                    <div class="text-gray-500">Chargement...</div>
                </div>
            </div>
        </div> -->

        <!-- Objectifs en cours -->
        @if(isset($objectifsActifs) && $objectifsActifs->count() > 0)
        <div class="mb-8">
            <h3 class="text-xl text-white mb-4">Objectifs en cours</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($objectifsActifs as $objectif)
                <div class="bg-gray-300 rounded-lg p-4">
                    <h4 class="font-semibold text-black mb-2">{{ $objectif->nom }}</h4>
                    <div class="mb-2">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $objectif->pourcentage }}%"></div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">
                        {{ number_format($objectif->getMontantAtteint(), 2, ',', ' ') }} € / {{ number_format($objectif->getMontantVise(), 2, ',', ' ') }} €
                    </p>
                    <p class="text-xs text-gray-500">
                        Échéance: {{ $objectif->date_echeance->format('d/m/Y') }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Add transaction button 
        <div class="flex justify-end mb-8">
            <a href="{{ route('transactions.create') }}" class="bg-gray-300 text-black hover:bg-white rounded-full px-6 py-2 transition-colors font-medium">
                Ajouter une transaction
            </a>
        </div> -->

        <!-- Recent transactions table -->
        <div>
            <h3 class="text-xl text-white mb-4">Dernières transactions</h3>
            <div class="bg-gray-300 shadow-none rounded-lg overflow-hidden">
                @if(isset($dernieresTransactions) && $dernieresTransactions->count() > 0)
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-purple-400">
                            <th class="text-black font-medium p-3 text-left">Nom</th>
                            <th class="text-black font-medium p-3 text-left">Date</th>
                            <th class="text-black font-medium p-3 text-left">Montant</th>
                            <th class="text-black font-medium p-3 text-left">Catégorie</th>
                            <th class="text-black font-medium p-3 text-left">Compte</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dernieresTransactions as $transaction)
                        <tr class="border-b border-purple-400">
                            <td class="text-black p-3">{{ $transaction->nom }}</td>
                            <td class="text-black p-3">{{ $transaction->date->format('d/m/Y') }}</td>
                            <td class="text-black p-3 {{ $transaction->montant > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->montant > 0 ? '+' : '' }}{{ number_format($transaction->montant, 2, ',', ' ') }} €
                            </td>
                            <td class="text-black p-3">{{ $transaction->categorie }}</td>
                            <td class="text-black p-3">{{ $transaction->compte->nom_compte ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="p-6 text-center">
                    <p class="text-gray-600 mb-4">Aucune transaction trouvée</p>
                    <a href="{{ route('transactions.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                        Créer votre première transaction
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="mt-8 grid grid-cols-3 gap-4 text-center">
            <div class="bg-gray-300 rounded-lg p-4">
                <p class="text-2xl font-bold text-black">{{ $nombreTransactions ?? 0 }}</p>
                <p class="text-sm text-gray-600">Transactions</p>
            </div>
            <div class="bg-gray-300 rounded-lg p-4">
                <p class="text-2xl font-bold text-black">{{ $nombreComptes ?? 0 }}</p>
                <p class="text-sm text-gray-600">Comptes</p>
            </div>
            <div class="bg-gray-300 rounded-lg p-4">
                <p class="text-2xl font-bold text-black">{{ $nombreObjectifs ?? 0 }}</p>
                <p class="text-sm text-gray-600">Objectifs</p>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Configuration des couleurs
const colors = {
    primary: '#7c3aed',
    secondary: '#6366f1',
    success: '#10b981',
    warning: '#f59e0b',
    danger: '#ef4444',
    info: '#3b82f6',
    purple: '#8b5cf6',
    pink: '#ec4899',
    teal: '#14b8a6',
    orange: '#f97316'
};

// Variables globales pour les graphiques
let monthlyChart, expensesChart, objectifsChart, comptesChart, yearlyChart;
let chartsLoaded = false;

// Fonction pour afficher/masquer les indicateurs de chargement
function showLoading(show = true) {
    const loadingIndicator = document.getElementById('loadingIndicator');
    const errorIndicator = document.getElementById('errorIndicator');
    
    if (show) {
        if (loadingIndicator) loadingIndicator.style.display = 'block';
        if (errorIndicator) errorIndicator.style.display = 'none';
    } else {
        if (loadingIndicator) loadingIndicator.style.display = 'none';
    }
    
    // Indicateurs individuels
    const loadingElements = ['monthlyLoading', 'expensesLoading', 'objectifsLoading', 'comptesLoading', 'yearlyLoading'];
    loadingElements.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.style.display = show ? 'flex' : 'none';
        }
    });
}

function showError(message = 'Erreur de chargement des graphiques') {
    const errorIndicator = document.getElementById('errorIndicator');
    const loadingIndicator = document.getElementById('loadingIndicator');
    
    if (errorIndicator) errorIndicator.style.display = 'block';
    if (loadingIndicator) loadingIndicator.style.display = 'none';
    showLoading(false);
    
    console.error('❌ Erreur graphiques:', message);
}

// Fonction pour tester les APIs
async function testAPIs() {
    console.log('🧪 Test des APIs...');
    
    const apis = [
        //{ url: '/dashboard/chart-data', name: 'Données mensuelles' },
        //{ url: '/dashboard/expenses-data', name: 'Dépenses' },
        { url: '/dashboard/objectifs-data', name: 'Objectifs' },
       // { url: '/dashboard/comptes-data', name: 'Comptes' }
    ];
    
    for (const api of apis) {
        try {
            console.log(`🔄 Test ${api.name}...`);
            const response = await fetch(api.url);
            const data = await response.json();
            
            if (response.ok) {
                console.log(`✅ ${api.name} - OK:`, data);
            } else {
                console.log(`❌ ${api.name} - Erreur ${response.status}:`, data);
            }
        } catch (error) {
            console.log(`❌ ${api.name} - Exception:`, error);
        }
    }
}

// Fonction pour charger les données et créer les graphiques
async function loadChartsData() {
    try {
        console.log('🔄 Chargement des données des graphiques...');
        showLoading(true);
        
        // Charger toutes les données en parallèle avec timeout
        const timeout = 10000; // 10 secondes
        
        const fetchWithTimeout = (url) => {
            return Promise.race([
                fetch(url),
                new Promise((_, reject) => 
                    setTimeout(() => reject(new Error('Timeout')), timeout)
                )
            ]);
        };
        
        const [monthlyResponse, expensesResponse, objectifsResponse, comptesResponse] = await Promise.all([
           // fetchWithTimeout('/dashboard/chart-data'),
            //fetchWithTimeout('/dashboard/expenses-data'),
            fetchWithTimeout('/dashboard/objectifs-data'),
            //fetchWithTimeout('/dashboard/comptes-data')
        ]);

        // Vérifier les réponses
       // if (!monthlyResponse.ok) throw new Error(`Erreur données mensuelles: ${monthlyResponse.status}`);
       // if (!expensesResponse.ok) throw new Error(`Erreur dépenses: ${expensesResponse.status}`);
        if (!objectifsResponse.ok) throw new Error(`Erreur objectifs: ${objectifsResponse.status}`);
       // if (!comptesResponse.ok) throw new Error(`Erreur comptes: ${comptesResponse.status}`);

        const [monthlyData, expensesData, objectifsData, comptesData] = await Promise.all([
            //monthlyResponse.json(),
           // expensesResponse.json(),
            objectifsResponse.json(),
           // comptesResponse.json()
        ]);

        console.log('📊 Données reçues:', { monthlyData, expensesData, objectifsData, comptesData });

        // Créer les graphiques avec les vraies données
       // createMonthlyChart(monthlyData);
       // createExpensesChart(expensesData);
        createObjectifsChart(objectifsData);
       // createComptesChart(comptesData);
       // createYearlyChart(monthlyData);

        showLoading(false);
        chartsLoaded = true;
        console.log('✅ Tous les graphiques ont été créés avec succès !');

    } catch (error) {
        console.error('❌ Erreur lors du chargement des données:', error);
        showError(error.message);
        
        // Afficher un message d'erreur dans les graphiques
        showErrorInCharts(error.message);
    }
}

// Fonction pour réessayer le chargement
function retryLoadCharts() {
    console.log('🔄 Nouvelle tentative de chargement...');
    chartsLoaded = false;
    loadChartsData();
}

// 1. Graphique évolution mensuelle
/*function createMonthlyChart(data) {
    const ctx = document.getElementById('monthlyChart');
    if (!ctx) {
        console.error('Canvas monthlyChart non trouvé');
        return;
    }
    
    if (monthlyChart) {
        monthlyChart.destroy();
    }

    // Vérifier si on a des données
    if (!data || data.length === 0) {
        showNoDataMessage(ctx.getContext('2d'), 'Aucune donnée mensuelle');
        return;
    }

    monthlyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.mois),
            datasets: [{
                label: 'Revenus',
                data: data.map(d => d.revenus),
                backgroundColor: colors.success + '80',
                borderColor: colors.success,
                borderWidth: 2
            }, {
                label: 'Dépenses',
                data: data.map(d => d.depenses),
                backgroundColor: colors.danger + '80',
                borderColor: colors.danger,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString('fr-FR') + ' €';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fr-FR') + ' €';
                        }
                    }
                }
            }
        }
    });
}

// 2. Graphique répartition des dépenses
function createExpensesChart(data) {
    const ctx = document.getElementById('expensesChart');
    if (!ctx) {
        console.error('Canvas expensesChart non trouvé');
        return;
    }
    
    if (expensesChart) {
        expensesChart.destroy();
    }

    if (!data || data.length === 0) {
        showNoDataMessage(ctx.getContext('2d'), 'Aucune dépense ce mois');
        return;
    }

    expensesChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(d => d.categorie),
            datasets: [{
                data: data.map(d => d.montant),
                backgroundColor: [
                    colors.primary,
                    colors.secondary,
                    colors.success,
                    colors.warning,
                    colors.danger,
                    colors.info,
                    colors.purple,
                    colors.pink,
                    colors.teal,
                    colors.orange
                ].slice(0, data.length),
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed.toLocaleString('fr-FR') + ' € (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}*/
// 3. Graphique progression des objectifs
function createObjectifsChart(data) {
    const ctx = document.getElementById('objectifsChart');
    if (!ctx) {
        console.error('Canvas objectifsChart non trouvé');
        return;
    }
    
    if (objectifsChart) {
        objectifsChart.destroy();
    }

    if (!data || data.length === 0) {
        showNoDataMessage(ctx.getContext('2d'), 'Aucun objectif actif');
        return;
    }

    objectifsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.nom),
            datasets: [{
                label: 'Progression (%)',
                data: data.map(d => d.progression),
                backgroundColor: data.map(d => 
                    d.progression >= 100 ? colors.success + '80' :
                    d.progression >= 80 ? colors.warning + '80' : 
                    d.progression >= 50 ? colors.info + '80' : colors.danger + '80'
                ),
                borderColor: data.map(d => 
                    d.progression >= 100 ? colors.success :
                    d.progression >= 80 ? colors.warning : 
                    d.progression >= 50 ? colors.info : colors.danger
                ),
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Progression: ' + context.parsed.x + '%';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
}

// 4. Graphique solde des comptes
/*function createComptesChart(data) {
    const ctx = document.getElementById('comptesChart');
    if (!ctx) {
        console.error('Canvas comptesChart non trouvé');
        return;
    }
    
    if (comptesChart) {
        comptesChart.destroy();
    }

    if (!data || data.length === 0) {
        showNoDataMessage(ctx.getContext('2d'), 'Aucun compte trouvé');
        return;
    }

    comptesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.nom),
            datasets: [{
                label: 'Solde',
                data: data.map(d => d.solde),
                backgroundColor: data.map((d, index) => {
                    const colorList = [colors.primary, colors.teal, colors.orange, colors.pink, colors.purple, colors.secondary];
                    return colorList[index % colorList.length] + '80';
                }),
                borderColor: data.map((d, index) => {
                    const colorList = [colors.primary, colors.teal, colors.orange, colors.pink, colors.purple, colors.secondary];
                    return colorList[index % colorList.length];
                }),
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed.y.toLocaleString('fr-FR') + ' €';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fr-FR') + ' €';
                        }
                    }
                }
            }
        }
    });
}

// 5. Graphique tendance annuelle
function createYearlyChart(data) {
    const ctx = document.getElementById('yearlyChart');
    if (!ctx) {
        console.error('Canvas yearlyChart non trouvé');
        return;
    }
    
    if (yearlyChart) {
        yearlyChart.destroy();
    }

    if (!data || data.length === 0) {
        showNoDataMessage(ctx.getContext('2d'), 'Aucune donnée pour la tendance');
        return;
    }

    yearlyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.mois),
            datasets: [{
                label: 'Revenus',
                data: data.map(d => d.revenus),
                borderColor: colors.success,
                backgroundColor: colors.success + '20',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Dépenses',
                data: data.map(d => d.depenses),
                borderColor: colors.danger,
                backgroundColor: colors.danger + '20',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Économies',
                data: data.map(d => d.revenus - d.depenses),
                borderColor: colors.primary,
                backgroundColor: colors.primary + '20',
                borderWidth: 3,
                fill: false,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString('fr-FR') + ' €';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fr-FR') + ' €';
                        }
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
}*/

// Fonction pour afficher un message quand il n'y a pas de données

// Fonction pour afficher les erreurs dans les graphiques
/*function showErrorInCharts(message = 'Erreur de chargement') {
    const chartIds = ['monthlyChart', 'expensesChart', 'objectifsChart', 'comptesChart', 'yearlyChart'];
    
    chartIds.forEach(id => {
        const canvas = document.getElementById(id);
        if (canvas) {
            const ctx = canvas.getContext('2d');
            showNoDataMessage(ctx, message);
        }
    });
}

// Charger les données au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    console.log('📊 Initialisation des graphiques avec les vraies données...');
    
    // Attendre un peu pour que la page soit complètement chargée
    setTimeout(() => {
        loadChartsData();
    }, 500);
});

// Fonction pour actualiser les graphiques
function refreshCharts() {
    if (chartsLoaded) {
        console.log('🔄 Actualisation des graphiques...');
        loadChartsData();
    }
}

// Actualiser les graphiques toutes les 5 minutes
setInterval(refreshCharts, 5 * 60 * 1000);*/
</script>
@endsection
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let objectifsChart;

    function createObjectifsChart(data) {
        const ctx = document.getElementById('objectifsChart').getContext('2d');

        if (objectifsChart) {
            objectifsChart.destroy();
        }

        if (!data || data.length === 0) {
            ctx.font = "16px Arial";
            ctx.fillText("Aucun objectif actif", 50, 100);
            return;
        }

        objectifsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.nom),
                datasets: [{
                    label: 'Progression (%)',
                    data: data.map(d => d.progression),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Progression : ' + context.parsed.x + '%';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const objectifsData = @json($objectifs);
        document.getElementById('objectifsLoading').style.display = 'none';
        createObjectifsChart(objectifsData);
    });
    // Script de debug à ajouter temporairement dans ta page dashboard
console.log('🔍 Début du diagnostic des graphiques...');

// Test des APIs une par une
async function testDashboardAPIs() {
    const apis = [
        { name: 'Chart Data', url: '/get-chart-data' },
        { name: 'Expenses Data', url: '/get-expenses-data' },
        { name: 'Objectifs Data', url: '/get-objectifs-data' },
        { name: 'Comptes Data', url: '/get-comptes-data' },
        { name: 'Debug APIs', url: '/debug-apis' }
    ];
    
    for (const api of apis) {
        try {
            console.log(`🔗 Test de ${api.name}...`);
            
            const response = await fetch(api.url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });
            
            console.log(`📊 ${api.name} - Status: ${response.status}`);
            
            if (response.ok) {
                const data = await response.json();
                console.log(`✅ ${api.name} - Données reçues:`, data);
                
                // Vérifier la structure des données
                if (data.success !== undefined) {
                    console.log(`🔍 ${api.name} - Success: ${data.success}`);
                    if (data.data) {
                        console.log(`📈 ${api.name} - Nombre d'éléments: ${Array.isArray(data.data) ? data.data.length : 'Objet'}`);
                    }
                }
            } else {
                console.error(`❌ ${api.name} - Erreur HTTP: ${response.status}`);
                const errorText = await response.text();
                console.error(`❌ ${api.name} - Détails:`, errorText);
            }
        } catch (error) {
            console.error(`💥 ${api.name} - Erreur réseau:`, error);
        }
        
        // Pause entre les requêtes
        await new Promise(resolve => setTimeout(resolve, 500));
    }
}

// Test des éléments DOM des graphiques
function testGraphicElements() {
    console.log('🎨 Vérification des éléments DOM des graphiques...');
    
    const graphicElements = [
        { name: 'Évolution mensuelle', selector: '#monthlyChart, #evolutionChart, .monthly-chart' },
        { name: 'Répartition dépenses', selector: '#expensesChart, #repartitionChart, .expenses-chart' },
        { name: 'Progression objectifs', selector: '#objectifsChart, #progressChart, .objectifs-chart' },
        { name: 'Solde comptes', selector: '#comptesChart, #accountsChart, .comptes-chart' }
    ];
    
    graphicElements.forEach(element => {
        const selectors = element.selector.split(', ');
        let found = false;
        
        selectors.forEach(selector => {
            const el = document.querySelector(selector);
            if (el) {
                console.log(`✅ ${element.name} - Élément trouvé: ${selector}`);
                console.log(`📐 ${element.name} - Dimensions: ${el.offsetWidth}x${el.offsetHeight}`);
                found = true;
            }
        });
        
        if (!found) {
            console.warn(`⚠️ ${element.name} - Aucun élément trouvé avec les sélecteurs: ${element.selector}`);
        }
    });
}

// Test des librairies de graphiques
function testGraphicLibraries() {
    console.log('📚 Vérification des librairies de graphiques...');
    
    const libraries = [
        { name: 'Chart.js', check: () => typeof Chart !== 'undefined' },
        { name: 'D3.js', check: () => typeof d3 !== 'undefined' },
        { name: 'ApexCharts', check: () => typeof ApexCharts !== 'undefined' },
        { name: 'Highcharts', check: () => typeof Highcharts !== 'undefined' }
    ];
    
    libraries.forEach(lib => {
        if (lib.check()) {
            console.log(`✅ ${lib.name} - Disponible`);
        } else {
            console.log(`❌ ${lib.name} - Non disponible`);
        }
    });
}

// Test du CSRF Token
function testCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (token) {
        console.log('🔐 CSRF Token trouvé:', token.substring(0, 10) + '...');
    } else {
        console.warn('⚠️ CSRF Token manquant - cela peut causer des erreurs 419');
    }
}

// Lancer tous les tests
async function runDiagnostic() {
    console.log('🚀 Diagnostic complet du dashboard...');
    
    testCSRFToken();
    testGraphicElements();
    testGraphicLibraries();
    
    await testDashboardAPIs();
    
    console.log('✨ Diagnostic terminé! Regarde les logs ci-dessus pour identifier les problèmes.');
}

// Auto-exécution du diagnostic
runDiagnostic();

// Fonction utilitaire pour tester une API spécifique
window.testAPI = function(apiUrl) {
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            console.log(`API ${apiUrl} - Réponse:`, data);
        })
        .catch(error => {
            console.error(`API ${apiUrl} - Erreur:`, error);
        });
};
</script>




