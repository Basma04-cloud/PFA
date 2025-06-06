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

            <!-- Debug info (à supprimer en production) -->
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                <p><strong>Debug Info:</strong></p>
                <p>Utilisateur ID: {{ Auth::id() }}</p>
                <p>Nombre de comptes: {{ $comptes->count() }}</p>
                @if($comptes->count() > 0)
                    <p>Comptes disponibles: 
                        @foreach($comptes as $compte)
                            {{ $compte->nom_compte }} (ID: {{ $compte->id }}){{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </p>
                @endif
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

                @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
                @endif

                @if ($comptes->isEmpty())
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                    <p>Vous n'avez aucun compte. <a href="{{ route('comptes.create') }}" class="underline">Créez d'abord un compte</a> pour pouvoir ajouter des transactions.</p>
                </div>
                @else

                <form method="POST" action="{{ route('transactions.store') }}" id="transactionForm">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-6">
                            <label for="nom" class="block text-purple-800 text-sm font-bold mb-2">Nom de la transaction *</label>
                            <input type="text" name="nom" id="nom" value="{{ old('nom') }}" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                        </div>

                        <div class="mb-6">
                            <label for="type" class="block text-purple-800 text-sm font-bold mb-2">Type *</label>
                            <select name="type" id="type" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                                <option value="">Sélectionner un type</option>
                                <option value="revenu" {{ old('type') == 'revenu' ? 'selected' : '' }}>Revenu</option>
                                <option value="depense" {{ old('type') == 'depense' ? 'selected' : '' }}>Dépense</option>
                                <option value="transfert" {{ old('type') == 'transfert' ? 'selected' : '' }}>Transfert</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label for="montant" class="block text-purple-800 text-sm font-bold mb-2">Montant *</label>
                            <input type="number" step="0.01" min="0.01" name="montant" id="montant" value="{{ old('montant') }}" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                            <p class="text-sm text-gray-600 mt-1">Entrez toujours un montant positif</p>
                        </div>

                        <div class="mb-6">
                            <label for="date" class="block text-purple-800 text-sm font-bold mb-2">Date *</label>
                            <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                        </div>

                        <div class="mb-6">
                            <label for="categorie" class="block text-purple-800 text-sm font-bold mb-2">Catégorie *</label>
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
                            <label for="compte_id" class="block text-purple-800 text-sm font-bold mb-2">Compte *</label>
                            <select name="compte_id" id="compte_id" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                                <option value="">Sélectionner un compte</option>
                                @foreach($comptes as $compte)
                                    <option value="{{ $compte->id }}" {{ old('compte_id') == $compte->id ? 'selected' : '' }}
                                            data-solde="{{ $compte->solde }}"
                                            data-nom="{{ $compte->nom_compte }}">
                                        {{ $compte->nom_compte }} ({{ number_format($compte->solde, 2, ',', ' ') }} €)
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-600 mt-1" id="compte-info"></p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block text-purple-800 text-sm font-bold mb-2">Description</label>
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

                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Debug: Log des données disponibles
console.log('Comptes disponibles:', @json($comptes));
console.log('User ID:', {{ Auth::id() }});

// Afficher les informations du compte sélectionné
document.getElementById('compte_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const infoElement = document.getElementById('compte-info');
    
    if (selectedOption.value) {
        const solde = selectedOption.dataset.solde;
        const nom = selectedOption.dataset.nom;
        infoElement.textContent = `Compte: ${nom} - Solde: ${parseFloat(solde).toLocaleString('fr-FR', {minimumFractionDigits: 2})} €`;
        infoElement.className = 'text-sm text-blue-600 mt-1';
        
        // Debug
        console.log('Compte sélectionné:', {
            id: selectedOption.value,
            nom: nom,
            solde: solde
        });
    } else {
        infoElement.textContent = '';
    }
});

// Validation avant soumission avec debug
document.getElementById('transactionForm').addEventListener('submit', function(e) {
    const compteId = document.getElementById('compte_id').value;
    const montant = parseFloat(document.getElementById('montant').value);
    const type = document.getElementById('type').value;
    
    console.log('Soumission du formulaire:', {
        compte_id: compteId,
        montant: montant,
        type: type
    });
    
    if (!compteId) {
        e.preventDefault();
        alert('Veuillez sélectionner un compte');
        return false;
    }
    
    if (montant <= 0) {
        e.preventDefault();
        alert('Le montant doit être supérieur à 0');
        return false;
    }
    
    // Vérification pour les dépenses
    if (type === 'depense') {
        const selectedOption = document.getElementById('compte_id').options[document.getElementById('compte_id').selectedIndex];
        const solde = parseFloat(selectedOption.dataset.solde);
        
        if (montant > solde) {
            const confirm = window.confirm(`Attention: Cette dépense (${montant}€) dépasse le solde disponible (${solde}€). Voulez-vous continuer?`);
            if (!confirm) {
                e.preventDefault();
                return false;
            }
        }
    }
});
</script>
@endsection

