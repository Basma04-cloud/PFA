@extends('layouts.app')

@section('title', 'Détails de l\'objectif')

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
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center">
                    <a href="{{ route('objectifs.index') }}" class="text-white hover:text-gray-300 mr-4">← Retour</a>
                    <h2 class="text-3xl text-white font-bold">{{ $objectif->nom }}</h2>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('objectifs.edit', $objectif->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Modifier
                    </a>
                    <form method="POST" action="{{ route('objectifs.corriger', $objectif->id) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                            Corriger statut
                        </button>
                    </form>
                </div>
            </div>

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Statut de l'objectif -->
            @if($objectif->is_atteint)
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🎉</span>
                    <div>
                        <h3 class="font-bold">Félicitations ! Objectif atteint !</h3>
                        @if($objectif->is_depasse)
                        <p>Vous avez même dépassé votre objectif de {{ $objectif->montant_exces_formatte }} !</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Détails de l'objectif -->
            <div class="bg-gray-300 rounded-lg overflow-hidden mb-8">
                <div class="bg-purple-600 text-white p-4">
                    <h3 class="text-xl font-bold">Mes objectifs financiers</h3>
                </div>
                
                <table class="w-full">
                    <tbody>
                        <tr class="border-b border-gray-400">
                            <td class="p-4 font-medium text-purple-800">Nom de l'objectif</td>
                            <td class="p-4">{{ $objectif->nom }}</td>
                        </tr>
                        <tr class="border-b border-gray-400">
                            <td class="p-4 font-medium text-purple-800">Montant visé</td>
                            <td class="p-4">{{ $objectif->montant_vise_formatte }}</td>
                        </tr>
                        <tr class="border-b border-gray-400">
                            <td class="p-4 font-medium text-purple-800">Montant atteint</td>
                            <td class="p-4">
                                <div class="flex items-center space-x-4">
                                    <span class="text-green-600 font-bold">{{ $objectif->montant_atteint_formatte }}</span>
                                    @if(!$objectif->is_atteint)
                                    <form method="POST" action="{{ route('objectifs.contribuer', $objectif->id) }}" class="flex items-center space-x-2">
                                        @csrf
                                        <input type="number" step="0.01" min="0.01" name="montant" placeholder="Montant" 
                                               class="px-3 py-1 border rounded focus:outline-none focus:border-purple-500" required>
                                        <button type="submit" class="bg-purple-600 text-white px-3 py-1 rounded hover:bg-purple-700">
                                            Ajouter
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-400">
                            <td class="p-4 font-medium text-purple-800">Progression</td>
                            <td class="p-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-1">
                                        <div class="bg-gray-200 rounded-full h-4 relative overflow-hidden">
                                            <div class="bg-{{ $objectif->is_atteint ? 'green' : 'purple' }}-600 h-4 rounded-full transition-all duration-300" 
                                                 style="width: {{ $objectif->pourcentage_barre }}%"></div>
                                            @if($objectif->is_depasse)
                                            <div class="absolute top-0 right-0 bg-yellow-400 h-4 px-2 flex items-center">
                                                <span class="text-xs font-bold">DÉPASSÉ</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="font-bold {{ $objectif->is_atteint ? 'text-green-600' : 'text-purple-600' }}">
                                        {{ number_format($objectif->pourcentage, 1) }}%
                                    </span>
                                </div>
                                @if(!$objectif->is_atteint)
                                <p class="text-sm text-gray-600 mt-1">
                                    Reste {{ $objectif->montant_restant_formatte }} à économiser
                                </p>
                                @else
                                <p class="text-sm text-green-600 mt-1">
                                    Objectif atteint ! 
                                    @if($objectif->is_depasse)
                                        Excédent de {{ $objectif->montant_exces_formatte }}
                                    @endif
                                </p>
                                @endif
                            </td>
                        </tr>
                        <tr class="border-b border-gray-400">
                            <td class="p-4 font-medium text-purple-800">Échéance</td>
                            <td class="p-4">
                                {{ $objectif->date_echeance->format('d/m/Y') }}
                                @php
                                    $joursRestants = now()->diffInDays($objectif->date_echeance, false);
                                @endphp
                                @if($joursRestants > 0)
                                    <span class="text-sm text-gray-600">({{ $joursRestants }} jours)</span>
                                @elseif($joursRestants == 0)
                                    <span class="text-sm text-orange-600">(Aujourd'hui !)</span>
                                @else
                                    <span class="text-sm text-red-600">(Échéance dépassée de {{ abs($joursRestants) }} jours)</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="p-4 font-medium text-purple-800">Statut</td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-{{ $objectif->statut_couleur }}-100 text-{{ $objectif->statut_couleur }}-800">
                                    {{ $objectif->statut_formatte }}
                                </span>
                            </td>
                        </tr>
                        @if($objectif->description)
                        <tr>
                            <td class="p-4 font-medium text-purple-800">Description</td>
                            <td class="p-4">{{ $objectif->description }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <div class="flex space-x-4">
                    <a href="{{ route('objectifs.edit', $objectif->id) }}" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        Modifier l'objectif
                    </a>
                    @if($objectif->statut === 'actif' && !$objectif->is_atteint)
                    <form method="POST" action="{{ route('objectifs.update', $objectif) }}" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="statut" value="abandonne">
                        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700" 
                                onclick="return confirm('Êtes-vous sûr de vouloir abandonner cet objectif ?')">
                            Abandonner
                        </button>
                    </form>
                    @endif
                </div>
                
                <form method="POST" action="{{ route('objectifs.destroy', $objectif) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700" 
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet objectif ?')">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


