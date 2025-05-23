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

        <!-- Cards -->
        <div class="grid grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-300 shadow-none rounded-lg p-6">
                <h3 class="text-center text-black font-semibold mb-4">Solde total</h3>
                <p class="text-center text-2xl font-bold text-black">
                    {{ isset($soldeTotal) ? number_format($soldeTotal, 2, ',', ' ') : '0,00' }} €
                </p>
            </div>

            <div class="bg-gray-300 shadow-none rounded-lg p-6">
                <h3 class="text-center text-black font-semibold mb-4">Dépenses du mois</h3>
                <p class="text-center text-2xl font-bold text-red-600">
                    {{ isset($depensesDuMois) ? number_format($depensesDuMois, 2, ',', ' ') : '0,00' }} €
                </p>
            </div>

            <div class="bg-gray-300 shadow-none rounded-lg p-6">
                <h3 class="text-center text-black font-semibold mb-4">Revenus du mois</h3>
                <p class="text-center text-2xl font-bold text-green-600">
                    {{ isset($revenusDuMois) ? number_format($revenusDuMois, 2, ',', ' ') : '0,00' }} €
                </p>
            </div>
        </div>

        <!-- Graph section -->
        <div class="mb-8">
            <h3 class="text-xl text-white mb-4">Graphique</h3>
            <div class="bg-white p-4 rounded-lg h-48 flex items-center justify-center">
                <p class="text-gray-500">Graphique des revenus et dépenses</p>
            </div>
        </div>

        <!-- Add transaction button -->
        <div class="flex justify-end mb-8">
            <a href="{{ route('transactions.create') }}" class="bg-gray-300 text-black hover:bg-white rounded-full px-6 py-2 transition-colors font-medium">
                Ajouter une transaction
            </a>
        </div>

        <!-- Recent transactions table -->
        <div>
            <h3 class="text-xl text-white mb-4">Dernières transactions</h3>
            <div class="bg-gray-300 shadow-none rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-purple-400">
                            <th class="text-black font-medium p-3 text-left">Nom</th>
                            <th class="text-black font-medium p-3 text-left">Date</th>
                            <th class="text-black font-medium p-3 text-left">Montant</th>
                            <th class="text-black font-medium p-3 text-left">Catégorie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($dernieresTransactions) && count($dernieresTransactions) > 0)
                            @foreach($dernieresTransactions as $transaction)
                            <tr class="border-b border-purple-400">
                                <td class="text-black p-3">{{ $transaction->nom }}</td>
                                <td class="text-black p-3">{{ $transaction->date }}</td>
                                <td class="text-black p-3 {{ $transaction->montant > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($transaction->montant, 2, ',', ' ') }} €
                                </td>
                                <td class="text-black p-3">{{ $transaction->categorie }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr class="border-b border-purple-400">
                                <td class="text-black p-3">Salaire</td>
                                <td class="text-black p-3">01/01/2024</td>
                                <td class="text-black p-3 text-green-600">2 000,00 €</td>
                                <td class="text-black p-3">Revenus</td>
                            </tr>
                            <tr class="border-b border-purple-400">
                                <td class="text-black p-3">Loyer</td>
                                <td class="text-black p-3">01/01/2024</td>
                                <td class="text-black p-3 text-red-600">-800,00 €</td>
                                <td class="text-black p-3">Logement</td>
                            </tr>
                            <tr class="border-b border-purple-400">
                                <td class="text-black p-3">Courses</td>
                                <td class="text-black p-3">02/01/2024</td>
                                <td class="text-black p-3 text-red-600">-120,50 €</td>
                                <td class="text-black p-3">Alimentation</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
