@extends('layouts.app')

@section('title', 'Modifier l\'objectif')

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
                <h2 class="text-3xl text-white font-bold">Modifier l'objectif</h2>
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

                <form method="POST" action="{{ route('objectifs.update', $objectif) }}" id="objectifForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-6">
                            <label for="nom" class="block text-purple-800 text-sm font-bold mb-2">Nom de l'objectif *</label>
                            <input type="text" name="nom" id="nom" value="{{ old('nom', $objectif->nom) }}" 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" 
                                   placeholder="Ex: Vacances d'été, Nouvelle voiture..." required>
                        </div>

                        <div class="mb-6">
                            <label for="montant_vise" class="block text-purple-800 text-sm font-bold mb-2">Montant visé *</label>
                            <input type="number" step="0.01" min="0.01" name="montant_vise" id="montant_vise" 
                                   value="{{ old('montant_vise', $objectif->montant_vise) }}" 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                            <p class="text-sm text-gray-600 mt-1">Montant que vous souhaitez atteindre</p>
                        </div>

                        <div class="mb-6">
                            <label for="date_echeance" class="block text-purple-800 text-sm font-bold mb-2">Date d'échéance *</label>
                            <input type="date" name="date_echeance" id="date_echeance" 
                                   value="{{ old('date_echeance', $objectif->date_echeance->format('Y-m-d')) }}" 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                            <p class="text-sm text-gray-600 mt-1">Date limite pour atteindre votre objectif</p>
                        </div>

                        <div class="mb-6">
                            <label for="montant_atteint" class="block text-purple-800 text-sm font-bold mb-2">Montant atteint</label>
                            <input type="number" step="0.01" min="0" name="montant_atteint" id="montant_atteint" 
                                   value="{{ old('montant_atteint', $objectif->montant_atteint) }}" 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
                            <p class="text-sm text-gray-600 mt-1">Montant actuellement économisé</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block text-purple-800 text-sm font-bold mb-2">Description</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" 
                                  placeholder="Décrivez votre objectif, pourquoi il est important pour vous...">{{ old('description', $objectif->description) }}</textarea>
                    </div>

                    <!-- Aperçu de l'objectif -->
                    <div class="mb-6 p-4 bg-purple-100 rounded-lg" id="apercu">
                        <h3 class="text-purple-800 font-bold mb-2">Aperçu de votre objectif :</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-semibold">Objectif :</span> <span id="apercu-nom">{{ $objectif->nom }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">Montant visé :</span> <span id="apercu-montant">{{ $objectif->montant_vise_formatte }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">Échéance :</span> <span id="apercu-date">{{ $objectif->date_echeance->format('d/m/Y') }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">Montant atteint :</span> <span id="apercu-atteint">{{ $objectif->montant_atteint_formatte }}</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="font-semibold">Progression :</span> <span id="apercu-progression" class="text-purple-600 font-bold">{{ number_format($objectif->pourcentage, 1) }}%</span>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('objectifs.index') }}" class="px-6 py-2 border border-gray-400 rounded-lg hover:bg-gray-100 transition-colors">
                            Annuler
                        </a>
                        <button type="submit" class="px-6 py-2 bg-purple-800 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            Mettre à jour l'objectif
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
    const montantAtteint = parseFloat(document.getElementById('montant_atteint').value) || 0;
    
    document.getElementById('apercu-nom').textContent = nom || '-';
    document.getElementById('apercu-montant').textContent = montantVise ? montantVise.toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' €' : '-';
    document.getElementById('apercu-date').textContent = dateEcheance ? new Date(dateEcheance).toLocaleDateString('fr-FR') : '-';
    document.getElementById('apercu-atteint').textContent = montantAtteint.toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' €';
    
    if (montantVise > 0) {
        const progression = (montantAtteint / montantVise) * 100;
        document.getElementById('apercu-progression').textContent = progression.toFixed(1) + '%';
    } else {
        document.getElementById('apercu-progression').textContent = '0%';
    }
}

// Écouter les changements dans les champs
['nom', 'montant_vise', 'date_echeance', 'montant_atteint'].forEach(id => {
    document.getElementById(id).addEventListener('input', updateApercu);
});

// Validation du formulaire
document.getElementById('objectifForm').addEventListener('submit', function(e) {
    const montantVise = parseFloat(document.getElementById('montant_vise').value);
    const montantAtteint = parseFloat(document.getElementById('montant_atteint').value) || 0;
    
    if (montantAtteint > montantVise && {{ $objectif->montant_atteint }} <= {{ $objectif->montant_vise }}) {
        e.preventDefault();
        alert('Le montant atteint ne peut pas être supérieur au montant visé (sauf si déjà dépassé)');
        return false;
    }
});
</script>
@endsection
