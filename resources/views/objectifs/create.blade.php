@extends('layouts.app')

@section('title', 'Nouvel objectif')

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
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center mb-8">
                <a href="{{ route('objectifs.index') }}" class="text-white hover:text-gray-300 mr-4">← Retour</a>
                <h2 class="text-3xl text-white font-bold">Nouvel objectif</h2>
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

                <form method="POST" action="{{ route('objectifs.store') }}" id="objectifForm">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-6">
                            <label for="nom" class="block text-purple-800 text-sm font-bold mb-2">Nom de l'objectif *</label>
                            <input type="text" name="nom" id="nom" value="{{ old('nom') }}" 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" 
                                   placeholder="Ex: Vacances d'été, Nouvelle voiture..." required>
                        </div>

                        <div class="mb-6">
                            <label for="montant_vise" class="block text-purple-800 text-sm font-bold mb-2">Montant visé *</label>
                            <input type="number" step="0.01" min="0.01" name="montant_vise" id="montant_vise" value="{{ old('montant_vise') }}" 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                            <p class="text-sm text-gray-600 mt-1">Montant que vous souhaitez atteindre</p>
                        </div>

                        <div class="mb-6">
                            <label for="date_echeance" class="block text-purple-800 text-sm font-bold mb-2">Date d'échéance *</label>
                            <input type="date" name="date_echeance" id="date_echeance" value="{{ old('date_echeance') }}" 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            <p class="text-sm text-gray-600 mt-1">Date limite pour atteindre votre objectif</p>
                        </div>

                        <div class="mb-6">
                            <label for="montant_initial" class="block text-purple-800 text-sm font-bold mb-2">Montant initial</label>
                            <input type="number" step="0.01" min="0" name="montant_initial" id="montant_initial" value="{{ old('montant_initial', '0') }}" 
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
                            <p class="text-sm text-gray-600 mt-1">Montant déjà économisé (optionnel)</p>
                        </div>
                    </div>
                    <div class="mb-6">
                           <label for="compte_id" class="block text-purple-800 text-sm font-bold mb-2">Compte source *</label>
                            <select name="compte_id" id="compte_id" required
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
                                <option value="">-- Sélectionnez un compte --</option>
                                @foreach ($comptes as $compte)
                                        <option value="{{ $compte->id }}" {{ old('compte_id') == $compte->id ? 'selected' : '' }}>
                                        {{ $compte->nom_compte }} ({{ $compte->type_compte}}) - {{ number_format($compte->solde, 2) }} MAD
                                </option>
                                @endforeach
                            </select>
                    </div>


                    <div class="mb-6">
                        <label for="description" class="block text-purple-800 text-sm font-bold mb-2">Description</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" 
                                  placeholder="Décrivez votre objectif, pourquoi il est important pour vous...">{{ old('description') }}</textarea>
                    </div>

                    <!-- Aperçu de l'objectif -->
                    <div class="mb-6 p-4 bg-purple-100 rounded-lg" id="apercu" style="display: none;">
                        <h3 class="text-purple-800 font-bold mb-2">Aperçu de votre objectif :</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-semibold">Objectif :</span> <span id="apercu-nom">-</span>
                            </div>
                            <div>
                                <span class="font-semibold">Montant visé :</span> <span id="apercu-montant">-</span>
                            </div>
                            <div>
                                <span class="font-semibold">Échéance :</span> <span id="apercu-date">-</span>
                            </div>
                            <div>
                                <span class="font-semibold">Montant initial :</span> <span id="apercu-initial">-</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="font-semibold">Montant restant :</span> <span id="apercu-restant" class="text-purple-600 font-bold">-</span>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('objectifs.index') }}" class="px-6 py-2 border border-gray-400 rounded-lg hover:bg-gray-100 transition-colors">
                            Annuler
                        </a>
                        <button type="submit" class="px-6 py-2 bg-purple-800 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            Créer l'objectif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Fonction pour mettre à jour l'aperçu
function updateApercu() {
    const nom = document.getElementById('nom').value;
    const montantVise = parseFloat(document.getElementById('montant_vise').value) || 0;
    const dateEcheance = document.getElementById('date_echeance').value;
    const montantInitial = parseFloat(document.getElementById('montant_initial').value) || 0;
    
    if (nom || montantVise || dateEcheance) {
        document.getElementById('apercu').style.display = 'block';
        
        document.getElementById('apercu-nom').textContent = nom || '-';
        document.getElementById('apercu-montant').textContent = montantVise ? montantVise.toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' €' : '-';
        document.getElementById('apercu-date').textContent = dateEcheance ? new Date(dateEcheance).toLocaleDateString('fr-FR') : '-';
        document.getElementById('apercu-initial').textContent = montantInitial.toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' €';
        
        const montantRestant = Math.max(montantVise - montantInitial, 0);
        document.getElementById('apercu-restant').textContent = montantRestant.toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' €';
    } else {
        document.getElementById('apercu').style.display = 'none';
    }
}

// Écouter les changements dans les champs
['nom', 'montant_vise', 'date_echeance', 'montant_initial'].forEach(id => {
    document.getElementById(id).addEventListener('input', updateApercu);
});

// Validation du formulaire
document.getElementById('objectifForm').addEventListener('submit', function(e) {
    const montantVise = parseFloat(document.getElementById('montant_vise').value);
    const montantInitial = parseFloat(document.getElementById('montant_initial').value) || 0;
    const dateEcheance = new Date(document.getElementById('date_echeance').value);
    const aujourd_hui = new Date();
    
    if (montantInitial > montantVise) {
        e.preventDefault();
        alert('Le montant initial ne peut pas être supérieur au montant visé');
        return false;
    }
    
    if (dateEcheance <= aujourd_hui) {
        e.preventDefault();
        alert('La date d\'échéance doit être dans le futur');
        return false;
    }
});
</script>
@endsection
