@extends('layouts.app')

@section('title', 'Modifier le Compte')

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
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center mb-8">
                <a href="{{ route('comptes.index') }}" class="text-white hover:text-gray-300 mr-4">← Retour</a>
                <h2 class="text-3xl text-white font-bold">Modifier le Compte</h2>
            </div>

            <div class="bg-gray-300 rounded-lg p-8">
                @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('comptes.update', $compte) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-6">
                            <label for="nom_compte" class="block text-purple-800 text-sm font-bold mb-2">Nom du compte *</label>
                            <input type="text" name="nom_compte" id="nom_compte" value="{{ old('nom_compte', $compte->nom_compte) }}" 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                        </div>

                        <div class="mb-6">
                            <label for="type_compte" class="block text-purple-800 text-sm font-bold mb-2">Type de compte *</label>
                            <select name="type_compte" id="type_compte" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                                <option value="">Sélectionner un type</option>
                                <option value="courant" {{ old('type_compte', $compte->type_compte) == 'courant' ? 'selected' : '' }}>Compte Courant</option>
                                <option value="epargne" {{ old('type_compte', $compte->type_compte) == 'epargne' ? 'selected' : '' }}>Compte Épargne</option>
                                <option value="credit" {{ old('type_compte', $compte->type_compte) == 'credit' ? 'selected' : '' }}>Compte Crédit</option>
                                <option value="investissement" {{ old('type_compte', $compte->type_compte) == 'investissement' ? 'selected' : '' }}>Compte Investissement</option>
                            </select>
                        </div>

                        <div class="mb-6 md:col-span-2">
                            <label for="solde" class="block text-purple-800 text-sm font-bold mb-2">Solde</label>
                            <input type="number" step="0.01" min="0" name="solde" id="solde" value="{{ old('solde', $compte->solde) }}" 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
                            <p class="text-sm text-gray-600 mt-1">Modifiez le solde si nécessaire</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block text-purple-800 text-sm font-bold mb-2">Description</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" 
                                  placeholder="Description optionnelle du compte...">{{ old('description', $compte->description ?? '') }}</textarea>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('comptes.index') }}" class="px-6 py-2 border border-gray-400 rounded-lg hover:bg-gray-100 transition-colors">
                            Annuler
                        </a>
                        <button type="submit" class="px-6 py-2 bg-purple-800 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
