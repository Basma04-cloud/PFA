@extends('layouts.app')

@section('title', 'Détails du compte')

@section('content')
<div class="flex h-screen bg-purple-400">
    <!-- Sidebar -->
    <div class="w-64 bg-purple-800 text-white p-6 flex flex-col">
        <h1 class="text-3xl font-bold mb-10">Dashboard</h1>
        <nav class="space-y-6 flex-1">
            <a href="{{ route('dashboard') }}" class="block text-xl hover:text-gray-300 transition-colors">Dashboard</a>
            <a href="{{ route('comptes.index') }}" class="block text-xl font-semibold text-purple-200">Comptes</a>
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
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center mb-8">
                <a href="{{ route('comptes.index') }}" class="text-white hover:text-gray-300 mr-4">← Retour</a>
                <h2 class="text-2xl text-white font-bold">{{ $compte->nom ?? 'Compte principal' }}</h2>
            </div>

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Carte d'information du compte -->
                <div class="bg-gray-200 rounded-lg p-6 col-span-1">
                    <h3 class="text-lg font-semibold mb-4">Informations</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Type</p>
                            <p class="font-medium">{{ ucfirst($compte->type ?? 'Bancaire') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Solde actuel</p>
                            <p class="text-xl font-bold {{ ($compte->solde ?? 1000) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($compte->solde ?? 1000, 2, ',', ' ') }} €
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Description</p>
                            <p>{{ $compte->description ?? 'Compte bancaire principal pour les dépenses quotidiennes.' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex space-x-2">
                        <a href="{{ route('comptes.edit', $compte->id ?? 1) }}" class="px-4 py-2 bg-purple-800 text-white rounded hover:bg-purple-700 transition-colors text-sm">
                            Modifier
                        </a>
                        <form method="POST" action="{{ route('comptes.destroy', $compte->id ?? 1) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-sm" onclick="return confirm('Êtes-vous sûr de vouloir désactiver ce compte ?')">
                                Désactiver
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Graphique d'évolution du solde -->
                <div class="bg-gray-200 rounded-lg p-6 col-span-2">
                    <h3 class="text-lg font-semibold mb-4">Évolution du solde</h3>
                    <div class="h-64 flex items-center justify-center bg-gray-100 rounded">
                        <p class="text-gray-500">Graphique d'évolution du solde</p>
                    </div>
                </div>
            </div>

            <!-- Dernières transactions -->
            <div class="bg-gray-200 rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Dernières transactions</h3>
                    <a href="{{ route('transactions.create') }}" class="text-purple-800 hover:text-purple-600 text-sm">
                        + Nouvelle transaction
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-300">
                                <th class="py-2 px-4 text-left">Date</th>
                                <th class="py-2 px-4 text-left">Description</th>
                                <th class="py-2 px-4 text-left">Catégorie</th>
                                <th class="py-2 px-4 text-right">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Transactions fictives -->
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-2 px-4">15/01/2024</td>
                                <td class="py-2 px-4">Courses supermarché</td>
                                <td class="py-2 px-4">Alimentation</td>
                                <td class="py-2 px-4 text-right text-red-600">-85,50 €</td>
                            </tr>
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-2 px-4">10/01/2024</td>
                                <td class="py-2 px-4">Restaurant</td>
                                <td class="py-2 px-4">Alimentation</td>
                                <td class="py-2 px-4 text-right text-red-600">-45,00 €</td>
                            </tr>
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-2 px-4">01/01/2024</td>
                                <td class="py-2 px-4">Salaire</td>
                                <td class="py-2 px-4">Revenus</td>
                                <td class="py-2 px-4 text-right text-green-600">+2 000,00 €</td>
                            </tr>
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-2 px-4">01/01/2024</td>
                                <td class="py-2 px-4">Loyer</td>
                                <td class="py-2 px-4">Logement</td>
                                <td class="py-2 px-4 text-right text-red-600">-800,00 €</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <a href="{{ route('transactions.index') }}" class="text-purple-800 hover:text-purple-600 text-sm">
                        Voir toutes les transactions →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
