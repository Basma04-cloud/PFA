@extends('layouts.app')

@section('title', 'Nouvelle Transaction')

@section('content')
<div class="flex h-screen bg-purple-400">
    <!-- Sidebar -->
    <div class="w-64 bg-purple-800 text-white p-6 flex flex-col">
        <h1 class="text-3xl font-bold mb-10">Dashboard</h1>
        <nav class="space-y-6 flex-1">
            <a href="{{ route('dashboard') }}" class="block text-xl hover:text-gray-300 transition-colors">Dashboard</a>
            <a href="{{ route('comptes.index') }}" class="block text-xl hover:text-gray-300 transition-colors">Comptes</a>
            <a href="{{ route('transactions.index') }}" class="block text-xl font-semibold text-purple-200">Transactions</a>
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
                <a href="{{ route('transactions.index') }}" class="text-white hover:text-gray-300 mr-4">← Retour</a>
                <h2 class="text-3xl text-white font-bold">Nouvelle Transaction</h2>
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

                <form method="POST" action="{{ route('transactions.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-6">
                            <label for="nom" class="block text-purple-800 text-sm font-bold mb-2">Nom de la transaction</label>
                            <input type="text" name="nom" id="nom" value="{{ old('nom') }}" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                        </div>

                        <div class="mb-6">
                            <label for="montant" class="block text-purple-800 text-sm font-bold mb-2">Montant</label>
                            <input type="number" step="0.01" name="montant" id="montant" value="{{ old('montant') }}" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                            <p class="text-sm text-gray-600 mt-1">Montant positif pour un revenu, négatif pour une dépense</p>
                        </div>

                        <div class="mb-6">
                            <label for="date" class="block text-purple-800 text-sm font-bold mb-2">Date</label>
                            <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                        </div>

                        <div class="mb-6">
                            <label for="categorie" class="block text-purple-800 text-sm font-bold mb-2">Catégorie</label>
                            <select name="categorie" id="categorie" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                                <option value="">Sélectionner une catégorie</option>
                                <option value="Alimentation" {{ old('categorie') == 'Alimentation' ? 'selected' : '' }}>Alimentation</option>
                                <option value="Logement" {{ old('categorie') == 'Logement' ? 'selected' : '' }}>Logement</option>
                                <option value="Transport" {{ old('categorie') == 'Transport' ? 'selected' : '' }}>Transport</option>
                                <option value="Divertissement" {{ old('categorie') == 'Divertissement' ? 'selected' : '' }}>Divertissement</option>
                                <option value="Santé" {{ old('categorie') == 'Santé' ? 'selected' : '' }}>Santé</option>
                                <option value="Éducation" {{ old('categorie') == 'Éducation' ? 'selected' : '' }}>Éducation</option>
                                <option value="Revenus" {{ old('categorie') == 'Revenus' ? 'selected' : '' }}>Revenus</option>
                                <option value="Épargne" {{ old('categorie') == 'Épargne' ? 'selected' : '' }}>Épargne</option>
                                <option value="Autres" {{ old('categorie') == 'Autres' ? 'selected' : '' }}>Autres</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label for="compte_id" class="block text-purple-800 text-sm font-bold mb-2">Compte</label>
                            <select name="compte_id" id="compte_id" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                                <option value="">Sélectionner un compte</option>
                                @foreach($comptes as $compte)
                                    <option value="{{ $compte->id }}" {{ old('compte_id') == $compte->id ? 'selected' : '' }}>
                                        {{ $compte->nom_compte }} ({{ number_format($compte->solde, 2, ',', ' ') }} €)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6">
                            <label for="type" class="block text-purple-800 text-sm font-bold mb-2">Type</label>
                            <select name="type" id="type" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                                <option value="">Sélectionner un type</option>
                                <option value="depense" {{ old('type') == 'depense' ? 'selected' : '' }}>Dépense</option>
                                <option value="revenu" {{ old('type') == 'revenu' ? 'selected' : '' }}>Revenu</option>
                                <option value="transfert" {{ old('type') == 'transfert' ? 'selected' : '' }}>Transfert</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block text-purple-800 text-sm font-bold mb-2">Description (optionnel)</label>
                        <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" placeholder="Détails supplémentaires sur la transaction...">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('transactions.index') }}" class="px-6 py-2 border border-gray-400 rounded-lg hover:bg-gray-100 transition-colors">
                            Annuler
                        </a>
                        <button type="submit" class="px-6 py-2 bg-purple-800 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            Créer la transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Script pour automatiquement définir le type basé sur le montant
document.getElementById('montant').addEventListener('input', function() {
    const montant = parseFloat(this.value);
    const typeSelect = document.getElementById('type');
    
    if (montant > 0) {
        typeSelect.value = 'revenu';
    } else if (montant < 0) {
        typeSelect.value = 'depense';
    }
});

// Script pour automatiquement ajuster le montant basé sur le type
document.getElementById('type').addEventListener('change', function() {
    const montantInput = document.getElementById('montant');
    const montant = parseFloat(montantInput.value);
    
    if (this.value === 'depense' && montant > 0) {
        montantInput.value = -Math.abs(montant);
    } else if (this.value === 'revenu' && montant < 0) {
        montantInput.value = Math.abs(montant);
    }
});
</script>
@endsection
