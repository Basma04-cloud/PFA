@extends('layouts.app')

@section('title', 'Mes objectifs financiers')

@section('content')
<div class="flex h-screen bg-purple-400">
    <!-- Sidebar -->
    <div class="w-64 bg-purple-800 text-white p-6 flex flex-col">
        <h1 class="text-3xl font-bold mb-10">Dashboard</h1>
        <nav class="space-y-6 flex-1">
            <a href="{{ route('dashboard') }}" class="block text-xl hover:text-gray-300 transition-colors">Dashboard</a>
            <a href="{{ route('comptes.index') }}" class="block text-xl hover:text-gray-300 transition-colors">Comptes</a>
            <a href="{{ route('transactions.index') }}" class="block text-xl hover:text-gray-300 transition-colors">Transactions</a>
            <a href="{{ route('objectifs.index') }}" class="block text-xl font-semibold text-purple-200">Objectifs</a>
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
            <div class="flex items-center">
                <h2 class="text-3xl text-white font-bold mr-4">Mes objectifs financiers</h2>
                <!-- Icône de cible -->
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
            </div>
            <a href="{{ route('objectifs.create') }}" class="bg-gray-300 text-black hover:bg-white rounded-full px-6 py-2 transition-colors font-medium">
                + Ajouter un objectif
            </a>
        </header>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
        @endif

        <!-- Tableau des objectifs selon le design Figma -->
        <div class="bg-gray-300 rounded-lg overflow-hidden shadow-lg">
            <table class="w-full">
                <thead>
                    <tr class="bg-purple-800 text-white">
                        <th class="py-4 px-6 text-left font-semibold">Élément</th>
                        <th class="py-4 px-6 text-left font-semibold">Détails</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($objectifs as $objectif)
                    <!-- Nom de l'objectif -->
                    <tr class="border-b border-gray-400">
                        <td class="py-3 px-6 font-medium text-purple-800">Nom de l'objectif</td>
                        <td class="py-3 px-6">{{ $objectif->nom }}</td>
                    </tr>
                    <!-- Montant visé -->
                    <tr class="border-b border-gray-400 bg-gray-200">
                        <td class="py-3 px-6 font-medium text-purple-800">Montant visé</td>
                        <td class="py-3 px-6 font-semibold">{{ number_format($objectif->montant_vise, 2, ',', ' ') }} €</td>
                    </tr>
                    <!-- Montant atteint -->
                    <tr class="border-b border-gray-400">
                        <td class="py-3 px-6 font-medium text-purple-800">Montant atteint</td>
                        <td class="py-3 px-6">
                            <div class="flex items-center space-x-4">
                                <span class="font-semibold text-green-600">{{ number_format($objectif->montant_atteint, 2, ',', ' ') }} €</span>
                                <form method="POST" action="{{ route('objectifs.contribuer', $objectif->id) }}" class="flex items-center space-x-2">
                                    @csrf
                                    <input type="number" step="0.01" name="montant" placeholder="Montant" class="w-24 px-2 py-1 text-sm border rounded">
                                    <button type="submit" class="bg-purple-800 text-white px-3 py-1 rounded text-sm hover:bg-purple-700">
                                        Ajouter
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <!-- Progression -->
                    <tr class="border-b border-gray-400 bg-gray-200">
                        <td class="py-3 px-6 font-medium text-purple-800">Progression</td>
                        <td class="py-3 px-6">
                            <div class="flex items-center space-x-3">
                                <div class="flex-1 bg-gray-300 rounded-full h-3">
                                    <div class="{{ $objectif->couleur_progression }} h-3 rounded-full transition-all duration-300" style="width: {{ $objectif->progression }}%"></div>
                                </div>
                                <span class="text-sm font-semibold">{{ $objectif->progression_formattee }}</span>
                            </div>
                        </td>
                    </tr>
                    <!-- Échéance -->
                    <tr class="border-b border-gray-400">
                        <td class="py-3 px-6 font-medium text-purple-800">Échéance</td>
                        <td class="py-3 px-6">
                            <div class="flex items-center space-x-2">
                                <span>{{ $objectif->date_echeance->format('d/m/Y') }}</span>
                                @if($objectif->est_en_retard)
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">En retard</span>
                                @elseif($objectif->est_atteint)
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Atteint</span>
                                @else
                                    <span class="text-sm text-gray-600">({{ abs($objectif->jours_restants) }} jours)</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <!-- Actions -->
                    <tr class="border-b-4 border-purple-400">
                        <td class="py-3 px-6 font-medium text-purple-800">Actions</td>
                        <td class="py-3 px-6">
                            <div class="flex space-x-2">
                                <a href="{{ route('objectifs.edit', $objectif->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">modifier</a>
                                <span class="text-gray-400">/</span>
                                <form method="POST" action="{{ route('objectifs.destroy', $objectif->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet objectif ?')">
                                        supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="py-8 text-center text-gray-500">
                            <p class="text-lg mb-4">Aucun objectif trouvé.</p>
                            <a href="{{ route('objectifs.create') }}" class="bg-purple-800 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                                Créer votre premier objectif
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Statistiques des objectifs -->
        @if($objectifs->count() > 0)
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-300 rounded-lg p-4 text-center">
                <h3 class="text-lg font-semibold text-purple-800 mb-2">Total des objectifs</h3>
                <p class="text-2xl font-bold">{{ $objectifs->count() }}</p>
            </div>
            <div class="bg-gray-300 rounded-lg p-4 text-center">
                <h3 class="text-lg font-semibold text-purple-800 mb-2">Objectifs atteints</h3>
                <p class="text-2xl font-bold text-green-600">{{ $objectifs->where('est_atteint', true)->count() }}</p>
            </div>
            <div class="bg-gray-300 rounded-lg p-4 text-center">
                <h3 class="text-lg font-semibold text-purple-800 mb-2">Montant total visé</h3>
                <p class="text-2xl font-bold">{{ number_format($objectifs->sum('montant_vise'), 2, ',', ' ') }} €</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
