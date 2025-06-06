@extends('layouts.app')

@section('title', 'Admin - Statistiques')

@section('content')

<div class="flex h-screen bg-purple-400">
    @include('admin.sidebar')
    

    <div class="flex-1 bg-purple-400 p-8 overflow-auto">
        <h2 class="text-2xl text-white mb-6">Statistiques</h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Utilisateurs par mois -->
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Évolution des utilisateurs</h3>
                <canvas id="usersChart"></canvas>
            </div>

            <!-- Revenus vs Dépenses -->
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Revenus vs Dépenses</h3>
                <canvas id="financeChart"></canvas>
            </div>

            <!-- Répartition des objectifs -->
            <div class="bg-white p-6 rounded shadow ">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Répartition des objectifs</h3>
                <canvas id="objectivesChart" style="max-width: 500px; height: 300px;"></canvas>


            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
     fetch('{{ route('admin.statistics.data') }}')
    .then(res => res.json())
    .then(data => {
        const months = data.monthly_users.map(item => item.date).reverse();
        const users = data.monthly_users.map(item => item.users).reverse();

        const financeLabels = data.monthly_revenue_expense.labels;
        const revenues = data.monthly_revenue_expense.revenus;
        const expenses = data.monthly_revenue_expense.depenses;

        const objectives = data.objective_distribution.map(item => item.objective);
        const counts = data.objective_distribution.map(item => item.count);
        new Chart(document.getElementById('usersChart'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Utilisateurs',
                    data: users,
                    borderColor: 'purple',
                    backgroundColor: 'rgba(128, 0, 128, 0.2)',
                    fill: true
                }]
            }
        });

        new Chart(document.getElementById('financeChart'), {
            type: 'bar',
            data: {
                labels: financeLabels,
                datasets: [
                    {
                        label: 'Revenus',
                        data: revenues,
                        backgroundColor: 'rgba(34, 197, 94, 0.7)'
                    },
                    {
                        label: 'Dépenses',
                        data: expenses,
                        backgroundColor: 'rgba(239, 68, 68, 0.7)'
                    }
                ]
            }
        });
        new Chart(document.getElementById('objectivesChart'), {
    type: 'doughnut',
    data: {
        labels: objectives,
        datasets: [{
            data: counts,
            backgroundColor: [
                '#7ddc94', // Atteint
                '#26689c', // Actif
                '#3da659'  // Abandonné
            ],
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#333',
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                }
            },
            title: {
                display: true,
                color: '#111',
                font: {
                    size: 16
                }
            }
        }
    }
});


    });


</script>
@endsection
