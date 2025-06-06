@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="flex h-screen bg-purple-400">
    <!-- Sidebar -->
    <div class="w-64 bg-purple-800 text-white p-6 flex flex-col">
        <h1 class="text-3xl font-bold mb-10">Dashboard</h1>
        <nav class="space-y-6 flex-1">
            <a href="{{ route('dashboard') }}" class="block text-xl hover:text-gray-300 transition-colors">Dashboard</a>
            <a href="{{ route('comptes.index') }}" class="block text-xl hover:text-gray-300 transition-colors">Comptes</a>
            <a href="{{ route('transactions.index') }}" class="block text-xl hover:text-gray-300 transition-colors">Transactions</a>
            <a href="{{ route('objectifs.index') }}" class="block text-xl hover:text-gray-300 transition-colors">Objectifs</a>
            <a href="{{ route('notifications.index') }}" class="block text-xl font-semibold text-purple-200 relative">
                Notifications
                @if(isset($stats['non_lues']) && $stats['non_lues'] > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        {{ $stats['non_lues'] }}
                    </span>
                @endif
            </a>
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
                <h2 class="text-3xl text-white font-bold">Notifications</h2>
                @if(isset($stats))
                <p class="text-white/80 mt-2">
                    {{ $stats['total'] }} notification(s) • {{ $stats['non_lues'] }} non lue(s) • {{ $stats['cette_semaine'] }} cette semaine
                </p>
                @endif
            </div>
            <div class="flex space-x-2">
                @if(isset($stats['non_lues']) && $stats['non_lues'] > 0)
                <form method="POST" action="{{ route('notifications.lire-tout') }}" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="bg-blue-600 text-white hover:bg-blue-700 rounded-full px-4 py-2 transition-colors font-medium">
                        Tout marquer comme lu
                    </button>
                </form>
                @endif
                <form method="POST" action="{{ route('notifications.supprimer-lues') }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white hover:bg-red-700 rounded-full px-4 py-2 transition-colors font-medium" onclick="return confirm('Supprimer toutes les notifications lues ?')">
                        Supprimer lues
                    </button>
                </form>
                <a href="{{ route('notifications.test') }}" class="bg-gray-600 text-white hover:bg-gray-700 rounded-full px-4 py-2 transition-colors font-medium">
                    Test
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

        @if(isset($error))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ $error }}
        </div>
        @endif

        <!-- Filtres -->
        <div class="mb-6">
            <div class="bg-gray-300 rounded-lg p-4">
                <h3 class="text-lg font-bold text-purple-800 mb-4">Filtrer les notifications</h3>
                <form action="{{ route('notifications.index') }}" method="GET" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" id="type" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
                            <option value="">Tous les types</option>
                            <option value="transaction" {{ request('type') == 'transaction' ? 'selected' : '' }}>Transactions</option>
                            <option value="objectif" {{ request('type') == 'objectif' ? 'selected' : '' }}>Objectifs</option>
                            <option value="budget" {{ request('type') == 'budget' ? 'selected' : '' }}>Budgets</option>
                            <option value="compte" {{ request('type') == 'compte' ? 'selected' : '' }}>Comptes</option>
                            <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>Informations</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut" id="statut" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
                            <option value="">Tous</option>
                            <option value="non_lu" {{ request('statut') == 'non_lu' ? 'selected' : '' }}>Non lues</option>
                            <option value="lu" {{ request('statut') == 'lu' ? 'selected' : '' }}>Lues</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="bg-purple-800 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            Filtrer
                        </button>
                        <a href="{{ route('notifications.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Notifications Grid -->
        <div class="grid grid-cols-1 gap-4">
            @if(isset($notifications) && $notifications->count() > 0)
                @foreach($notifications as $notification)
                <div class="bg-gray-300 rounded-lg p-6 shadow-lg {{ $notification->lu ? 'opacity-75' : 'border-l-4 border-blue-500' }}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <span class="text-2xl mr-3">{{ $notification->icone }}</span>
                                @if(!$notification->lu)
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                @endif
                                <h3 class="text-lg font-bold text-purple-800">{{ $notification->titre }}</h3>
                                <span class="ml-2 px-2 py-1 text-xs rounded-full bg-{{ $notification->couleur }}-100 text-{{ $notification->couleur }}-800">
                                    {{ ucfirst($notification->type) }}
                                </span>
                            </div>
                            <p class="text-gray-700 mb-2">{{ $notification->message }}</p>
                            <div class="flex items-center text-sm text-gray-500 space-x-4">
                                <span>{{ $notification->time_ago }}</span>
                                <span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                @if($notification->lu)
                                    <span class="text-green-600">✓ Lu le {{ $notification->lu_at->format('d/m/Y H:i') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex space-x-2 ml-4">
                            @if(!$notification->lu)
                                <form method="POST" action="{{ route('notifications.lire', $notification->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors">
                                        Marquer comme lu
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition-colors" onclick="return confirm('Supprimer cette notification ?')">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                @if($notifications->hasPages())
                <div class="mt-6">
                    {{ $notifications->appends(request()->query())->links() }}
                </div>
                @endif
            @else
                <div class="bg-gray-300 rounded-lg p-8 text-center">
                    <div class="text-6xl mb-4">📭</div>
                    <h3 class="text-xl font-bold text-purple-800 mb-2">Aucune notification</h3>
                    <p class="text-gray-600 mb-4">Vous n'avez aucune notification pour le moment.</p>
                    <a href="{{ route('notifications.test') }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                        Créer une notification de test
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

