@extends('layouts.app')

@section('title', 'Mes Objectifs')

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
            <div>
                <h2 class="text-3xl text-white font-bold">Mes Objectifs</h2>
                @if(isset($stats))
                <p class="text-white/80 mt-2">
                    {{ $stats['total'] }} objectif(s) • {{ $stats['atteints'] }} atteint(s) • {{ $stats['actifs'] }} en cours
                </p>
                @endif
            </div>
            <div class="flex space-x-4">
                @if(isset($objectifs) && $objectifs->where('statut', 'actif')->count() > 0)
                <form method="POST" action="{{ route('objectifs.corriger-tous') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-yellow-600 text-white hover:bg-yellow-700 rounded-full px-4 py-2 transition-colors font-medium">
                        Corriger tous les statuts
                    </button>
                </form>
                @endif
                <a href="{{ route('objectifs.create') }}" class="bg-gray-300 text-black hover:bg-white rounded-full px-6 py-2 transition-colors font-medium">
                    Nouvel objectif
                </a>
            </div>
        </header>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
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

        <!-- Statistiques globales -->
        @if(isset($stats) && $stats['total'] > 0)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-300 rounded-lg p-6 text-center">
                <h3 class="text-lg font-bold text-purple-800 mb-2">Total des objectifs</h3>
                <p class="text-3xl font-bold text-black">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-gray-300 rounded-lg p-6 text-center">
                <h3 class="text-lg font-bold text-purple-800 mb-2">Objectifs atteints</h3>
                <p class="text-3xl font-bold text-green-600">{{ $stats['atteints'] }}</p>
            </div>
            <div class="bg-gray-300 rounded-lg p-6 text-center">
                <h3 class="text-lg font-bold text-purple-800 mb-2">En cours</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['actifs'] }}</p>
            </div>
            <div class="bg-gray-300 rounded-lg p-6 text-center">
                <h3 class="text-lg font-bold text-purple-800 mb-2">Montant total visé</h3>
                <p class="text-2xl font-bold text-black">{{ number_format($stats['montant_total_vise'], 2, ',', ' ') }} €</p>
            </div>
        </div>
        @endif

        <!-- Filtres -->
        <div class="mb-6">
            <div class="bg-gray-300 rounded-lg p-4">
                <h3 class="text-lg font-bold text-purple-800 mb-4">Filtrer les objectifs</h3>
                <form action="{{ route('objectifs.index') }}" method="GET" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut" id="statut" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
                            <option value="">Tous les statuts</option>
                            <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>En cours</option>
                            <option value="atteint" {{ request('statut') == 'atteint' ? 'selected' : '' }}>Atteints</option>
                            <option value="abandonne" {{ request('statut') == 'abandonne' ? 'selected' : '' }}>Abandonnés</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label for="tri" class="block text-sm font-medium text-gray-700 mb-1">Trier par</label>
                        <select name="tri" id="tri" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
                            <option value="date_echeance" {{ request('tri') == 'date_echeance' ? 'selected' : '' }}>Échéance</option>
                            <option value="progression" {{ request('tri') == 'progression' ? 'selected' : '' }}>Progression</option>
                            <option value="montant_vise" {{ request('tri') == 'montant_vise' ? 'selected' : '' }}>Montant visé</option>
                            <option value="created_at" {{ request('tri') == 'created_at' ? 'selected' : '' }}>Date de création</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="bg-purple-800 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            Filtrer
                        </button>
                        <a href="{{ route('objectifs.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des objectifs -->
        @if(isset($objectifs) && $objectifs->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($objectifs as $objectif)
                <div class="bg-gray-300 rounded-lg overflow-hidden shadow-lg {{ $objectif->is_atteint ? 'border-l-4 border-green-500' : ($objectif->statut === 'abandonne' ? 'border-l-4 border-red-500' : 'border-l-4 border-blue-500') }}">
                    <!-- En-tête de la carte -->
                    <div class="p-6 pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-purple-800 mb-2">{{ $objectif->nom }}</h3>
                                @if($objectif->description)
                                <p class="text-gray-600 text-sm mb-3">{{ Str::limit($objectif->description, 100) }}</p>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-{{ $objectif->statut_couleur }}-100 text-{{ $objectif->statut_couleur }}-800">
                                    {{ $objectif->statut_formatte }}
                                </span>
                                @if($objectif->is_atteint)
                                    <span class="text-2xl">🎉</span>
                                @elseif($objectif->pourcentage >= 80)
                                    <span class="text-2xl">🎯</span>
                                @endif
                            </div>
                        </div>

                        <!-- Montants -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-600">Montant visé</p>
                                <p class="text-lg font-bold text-black">{{ $objectif->montant_vise_formatte }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Montant atteint</p>
                                <p class="text-lg font-bold {{ $objectif->is_atteint ? 'text-green-600' : 'text-blue-600' }}">
                                    {{ $objectif->montant_atteint_formatte }}
                                </p>
                            </div>
                        </div>

                        <!-- Barre de progression -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Progression</span>
                                <span class="text-sm font-bold {{ $objectif->is_atteint ? 'text-green-600' : 'text-purple-600' }}">
                                    {{ number_format($objectif->pourcentage, 1) }}%
                                </span>
                            </div>
                            <div class="bg-gray-200 rounded-full h-3 relative overflow-hidden">
                                <div class="bg-{{ $objectif->is_atteint ? 'green' : 'purple' }}-600 h-3 rounded-full transition-all duration-500" 
                                     style="width: {{ min($objectif->pourcentage, 100) }}%"></div>
                                @if($objectif->is_depasse)
                                <div class="absolute top-0 right-0 bg-yellow-400 h-3 px-2 flex items-center">
                                    <span class="text-xs font-bold text-yellow-800">DÉPASSÉ</span>
                                </div>
                                @endif
                            </div>
                            @if(!$objectif->is_atteint)
                            <p class="text-xs text-gray-600 mt-1">
                                Reste {{ $objectif->montant_restant_formatte }} à économiser
                            </p>
                            @else
                            <p class="text-xs text-green-600 mt-1">
                                Objectif atteint ! 
                                @if($objectif->is_depasse)
                                    Excédent de {{ number_format($objectif->montant_exces, 2, ',', ' ') }} €
                                @endif
                            </p>
                            @endif
                        </div>

                        <!-- Échéance -->
                        <div class="mb-4">
                            @php
                                $joursRestants = now()->diffInDays($objectif->date_echeance, false);
                            @endphp
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Échéance :</span> 
                                {{ $objectif->date_echeance->format('d/m/Y') }}
                                @if($objectif->statut === 'actif')
                                    @if($joursRestants > 0)
                                        <span class="text-blue-600">({{ $joursRestants }} jour{{ $joursRestants > 1 ? 's' : '' }} restant{{ $joursRestants > 1 ? 's' : '' }})</span>
                                    @elseif($joursRestants == 0)
                                        <span class="text-orange-600 font-bold">(Aujourd'hui !)</span>
                                    @else
                                        <span class="text-red-600 font-bold">(Échéance dépassée de {{ abs($joursRestants) }} jour{{ abs($joursRestants) > 1 ? 's' : '' }})</span>
                                    @endif
                                @endif
                            </p>
                        </div>

                        <!-- Contribution rapide -->
                        @if($objectif->statut === 'actif' && !$objectif->is_atteint)
                        <div class="mb-4 p-3 bg-purple-50 rounded-lg">
                            <form method="POST" action="{{ route('objectifs.contribuer', $objectif->id) }}" class="flex items-center space-x-2">
                                @csrf
                                <input type="number" step="0.01" min="0.01" max="99999.99" name="montant" placeholder="Montant à ajouter" 
                                       class="flex-1 px-3 py-2 border rounded focus:outline-none focus:border-purple-500 text-sm" required>
                                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition-colors text-sm font-medium">
                                    Ajouter
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="bg-gray-200 px-6 py-4 flex justify-between items-center">
                        <div class="flex space-x-2">
                            <a href="{{ route('objectifs.show', $objectif->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Voir détails
                            </a>
                            <span class="text-gray-400">•</span>
                            <a href="{{ route('objectifs.edit', $objectif->id) }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                Modifier
                            </a>
                            @if($objectif->statut === 'actif' && !$objectif->is_atteint)
                            <span class="text-gray-400">•</span>
                            <form method="POST" action="{{ route('objectifs.corriger', $objectif->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
                                    Corriger
                                </button>
                            </form>
                            @endif
                        </div>
                        
                        <form method="POST" action="{{ route('objectifs.destroy', $objectif->id) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium" 
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet objectif ?')">
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination si nécessaire -->
            @if(method_exists($objectifs, 'hasPages') && $objectifs->hasPages())
            <div class="mt-8">
                {{ $objectifs->appends(request()->query())->links() }}
            </div>
            @endif

        @else
            <!-- État vide -->
            <div class="bg-gray-300 rounded-lg p-12 text-center">
                <div class="text-8xl mb-6">🎯</div>
                <h3 class="text-2xl font-bold text-purple-800 mb-4">Aucun objectif pour le moment</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Commencez à planifier votre avenir financier en créant votre premier objectif d'épargne !
                </p>
                <a href="{{ route('objectifs.create') }}" class="bg-purple-600 text-white px-8 py-3 rounded-lg hover:bg-purple-700 transition-colors font-medium inline-block">
                    Créer mon premier objectif
                </a>
            </div>
        @endif

        <!-- Conseils -->
        @if(isset($objectifs) && $objectifs->count() > 0)
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-bold text-blue-800 mb-3">💡 Conseils pour atteindre vos objectifs</h3>
            <ul class="text-blue-700 space-y-2 text-sm">
                <li>• <strong>Soyez régulier :</strong> Ajoutez de petits montants régulièrement plutôt qu'une grosse somme d'un coup</li>
                <li>• <strong>Automatisez :</strong> Programmez des virements automatiques vers vos objectifs</li>
                <li>• <strong>Révisez :</strong> Ajustez vos objectifs si nécessaire, ils doivent rester réalistes</li>
                <li>• <strong>Célébrez :</strong> Récompensez-vous quand vous atteignez un objectif !</li>
            </ul>
        </div>
        @endif
    </div>
</div>

<script>
// Animation des barres de progression au chargement
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('[style*="width:"]');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });
});

// Validation des formulaires de contribution
document.querySelectorAll('form[action*="contribuer"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        const montantInput = this.querySelector('input[name="montant"]');
        const montant = parseFloat(montantInput.value);
        
        if (montant <= 0) {
            e.preventDefault();
            alert('Le montant doit être supérieur à 0');
            return false;
        }
        
        if (montant > 99999.99) {
            e.preventDefault();
            alert('Le montant ne peut pas dépasser 99 999,99 €');
            return false;
        }
    });
});

// Confirmation pour les actions de suppression
document.querySelectorAll('form[action*="destroy"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet objectif ? Cette action est irréversible.')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>

<style>
/* Animations personnalisées */
.transition-all {
    transition: all 0.3s ease;
}

/* Effet hover sur les cartes */
.bg-gray-300:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Animation de la barre de progression */
.bg-purple-600, .bg-green-600 {
    transition: width 0.8s ease-in-out;
}

/* Style pour les objectifs atteints */
.border-green-500 {
    box-shadow: 0 0 0 1px rgba(34, 197, 94, 0.2);
}

/* Style pour les objectifs abandonnés */
.border-red-500 {
    opacity: 0.8;
}

/* Responsive */
@media (max-width: 768px) {
    .grid-cols-2 {
        grid-template-columns: 1fr;
    }
    
    .lg\:grid-cols-2 {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
