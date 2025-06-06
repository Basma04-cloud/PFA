@extends('layouts.app')

@section('title', 'Dashboard Administrateur')

@section('content')
<div class="flex h-screen bg-purple-400">
    <!-- Sidebar -->
    <div class="w-80 bg-purple-800 text-white p-6 flex flex-col">
        <h1 class="text-3xl font-bold mb-10 text-center">💼 Admin Panel</h1>
        
        <!-- Section Administration -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <span class="text-lg font-semibold text-purple-200">🔧 Administration</span>
            </div>
            <nav class="space-y-3 ml-4">
                <a href="{{ route('admin.dashboard') }}" 
                   class="block text-lg {{ request()->routeIs('admin.dashboard') ? 'text-white font-semibold bg-purple-600 px-3 py-2 rounded' : 'text-purple-200 hover:text-white transition-colors' }}">
                    📊 Dashboard Admin
                </a>
                <a href="{{ route('admin.users') }}" 
                   class="block text-lg {{ request()->routeIs('admin.users.*') ? 'text-white font-semibold bg-purple-600 px-3 py-2 rounded' : 'text-purple-200 hover:text-white transition-colors' }}">
                    👥 Utilisateurs
                </a>
                <a href="{{ route('admin.statistics') }}" 
                   class="block text-lg {{ request()->routeIs('admin.statistics') ? 'text-white font-semibold bg-purple-600 px-3 py-2 rounded' : 'text-purple-200 hover:text-white transition-colors' }}">
                    📈 Statistiques
                </a>
                <a href="{{ route('admin.invitations.index') }}" 
                   class="block text-lg {{ request()->routeIs('admin.invitations.index') ? 'text-white font-semibold bg-purple-600 px-3 py-2 rounded' : 'text-purple-200 hover:text-white transition-colors' }}">
                    📋 Invitations
                </a>
            </nav>
        </div>

        <!-- Séparateur -->
        <div class="border-t border-purple-600 mb-6"></div>

        <!-- Section Mes Finances -->
        <div class="flex-1">
            <div class="flex items-center mb-4">
                <span class="text-lg font-semibold text-purple-200">👤 Personnel</span>
            </div>
            <nav class="space-y-3 ml-4">
                <a href="{{ route('dashboard') }}" 
                   class="block text-lg {{ request()->routeIs('dashboard') ? 'text-white font-semibold bg-green-600 px-3 py-2 rounded' : 'text-purple-200 hover:text-white transition-colors' }}">
                    🏠 Mon Dashboard Personnel
                </a>
                <!--<a href="{{ route('comptes.index') }}" 
                   class="block text-lg {{ request()->routeIs('comptes') ? 'text-white font-semibold bg-green-600 px-3 py-2 rounded' : 'text-purple-200 hover:text-white transition-colors' }}">
                    🏦 Mes Comptes
                </a>
                <a href="{{ route('transactions.index') }}" 
                   class="block text-lg {{ request()->routeIs('transactions') ? 'text-white font-semibold bg-green-600 px-3 py-2 rounded' : 'text-purple-200 hover:text-white transition-colors' }}">
                    💳 Mes Transactions
                </a>
                <a href="{{ route('objectifs.index') }}" 
                   class="block text-lg {{ request()->routeIs('objectifs') ? 'text-white font-semibold bg-green-600 px-3 py-2 rounded' : 'text-purple-200 hover:text-white transition-colors' }}">
                    🎯 Mes Objectifs
                </a>
                <a href="{{ route('notifications.index') }}" 
                   class="block text-lg {{ request()->routeIs('notifications') ? 'text-white font-semibold bg-green-600 px-3 py-2 rounded' : 'text-purple-200 hover:text-white transition-colors' }}">
                    🔔 Notifications
                </a>
                <a href="{{ route('profil.index') }}" 
                   class="block text-lg {{ request()->routeIs('profil') ? 'text-white font-semibold bg-green-600 px-3 py-2 rounded' : 'text-purple-200 hover:text-white transition-colors' }}">
                    👤 Mon Profil
                </a>-->
            </nav>
        </div>

        <!-- Déconnexion -->
        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button type="submit" class="w-full text-lg bg-red-600 hover:bg-red-700 px-4 py-2 rounded transition-colors">
                🚪 Déconnexion
            </button>
        </form>
    </div>

    <!-- Main content -->
    <div class="flex-1 bg-purple-400 p-8 overflow-auto">
        <!-- Indicateur de mode -->
        <div class="absolute top-4 right-8 px-4 py-2 rounded-full text-sm font-semibold text-white
            {{ request()->routeIs('admin.personal.*') ? 'bg-green-600' : 'bg-purple-600' }}">
            {{ request()->routeIs('admin.personal.*') ? '💰 MODE PERSONNEL' : '🔧 MODE ADMINISTRATION' }}
        </div>

        <header class="flex justify-between items-center mb-8 mt-8">
            @if(request()->routeIs('admin.dashboard'))
                <h2 class="text-2xl text-white">Tableau de bord administrateur</h2>
            @elseif(request()->routeIs('dashboard'))
                <h2 class="text-2xl text-white">Mon Dashboard Personnel</h2>
            @elseif(request()->routeIs('admin.users'))
                <h2 class="text-2xl text-white">Gestion des Utilisateurs</h2>
            @elseif(request()->routeIs('comptes.index'))
                <h2 class="text-2xl text-white">Mes Comptes</h2>
            @elseif(request()->routeIs('transactions.index'))
                <h2 class="text-2xl text-white">Mes Transactions</h2>
            @elseif(request()->routeIs('objectifs.index'))
                <h2 class="text-2xl text-white">Mes Objectifs</h2>
            @else
                <h2 class="text-2xl text-white">Dashboard</h2>
            @endif
            
            <div class="text-white text-xl">
                Bienvenue{{ Auth::check() ? ', ' . Auth::user()->name : '' }}
            </div>
        </header>

        @if(isset($error))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ $error }}
            <button onclick="testAPIs()" class="ml-4 bg-red-600 text-white px-3 py-1 rounded text-sm">
                Tester les APIs
            </button>
        </div>
        @endif

        <!-- Indicateur de chargement -->
        <div id="loadingIndicator" class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4" style="display: none;">
            🔄 Chargement des graphiques en cours...
        </div>

        <!-- Indicateur d'erreur -->
        <div id="errorIndicator" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" style="display: none;">
            ❌ Erreur de chargement des graphiques. 
            <button onclick="retryLoadCharts()" class="ml-2 bg-red-600 text-white px-3 py-1 rounded text-sm">
                Réessayer
            </button>
            <button onclick="testAPIs()" class="ml-2 bg-blue-600 text-white px-3 py-1 rounded text-sm">
                Diagnostiquer
            </button>
        </div>

        <!-- Contenu conditionnel selon la route -->
        @if(request()->routeIs('admin.dashboard'))
            @include('admin.partials.admin-dashboard-content')
        @elseif(request()->routeIs('dashboard'))
            @include('admin.partials.personal-dashboard-content')
        @elseif(request()->routeIs('admin.users'))
            @include('admin.partials.users-content')
        @else
            <!-- Contenu par défaut ou autres sections -->
            @yield('section-content')
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Vos scripts existants pour testAPIs(), retryLoadCharts(), etc.
    function testAPIs() {
        // Logique de test des APIs
        console.log('Test des APIs...');
    }

    function retryLoadCharts() {
        // Logique de rechargement des graphiques
        console.log('Rechargement des graphiques...');
    }
</script>
@endpush
@endsection