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
            <a href="{{ route('notifications.index') }}" class="block text-xl font-semibold text-purple-200">Notifications</a>
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
            <h2 class="text-3xl text-white font-bold">Notifications</h2>
            <form method="POST" action="{{ route('notifications.lire-tout') }}">
                @csrf
                @method('PUT')
                <button type="submit" class="bg-gray-300 text-black hover:bg-white rounded-full px-6 py-2 transition-colors font-medium">
                    Tout marquer comme lu
                </button>
            </form>
        </header>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
        @endif

        <!-- Notifications Grid -->
        <div class="grid grid-cols-1 gap-4">
            @if(isset($notifications) && count($notifications) > 0)
                @foreach($notifications as $notification)
                <div class="bg-gray-300 rounded-lg p-6 shadow-lg {{ $notification->lu ? 'opacity-75' : '' }}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                @if(!$notification->lu)
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                @endif
                                <h3 class="text-lg font-bold text-purple-800">{{ $notification->titre }}</h3>
                            </div>
                            <p class="text-gray-700 mb-2">{{ $notification->message ?? 'Détails de la notification' }}</p>
                            <p class="text-sm text-gray-500">{{ $notification->created_at ? $notification->created_at->format('d/m/Y H:i') : 'Date' }}</p>
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
            @else
                <!-- Notifications par défaut -->
                <div class="bg-gray-300 rounded-lg p-6 shadow-lg">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <h3 class="text-lg font-bold text-purple-800">Objectif atteint !</h3>
                            </div>
                            <p class="text-gray-700 mb-2">Félicitations ! Vous avez atteint votre objectif "Vacances d'été".</p>
                            <p class="text-sm text-gray-500">Aujourd'hui 14:30</p>
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors">
                                Marquer comme lu
                            </button>
                            <button class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition-colors">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-300 rounded-lg p-6 shadow-lg opacity-75">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-purple-800 mb-2">Budget dépassé</h3>
                            <p class="text-gray-700 mb-2">Attention, vous avez dépassé votre budget "Alimentation" ce mois-ci.</p>
                            <p class="text-sm text-gray-500">Hier 09:15</p>
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition-colors">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-300 rounded-lg p-6 shadow-lg opacity-75">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-purple-800 mb-2">Nouvelle transaction</h3>
                            <p class="text-gray-700 mb-2">Une nouvelle transaction de -85,50 € a été ajoutée à votre compte.</p>
                            <p class="text-sm text-gray-500">15/01/2024 16:45</p>
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition-colors">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-300 rounded-lg p-6 shadow-lg opacity-75">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-purple-800 mb-2">Rappel d'objectif</h3>
                            <p class="text-gray-700 mb-2">N'oubliez pas de contribuer à votre objectif "Nouveau PC".</p>
                            <p class="text-sm text-gray-500">13/01/2024 10:00</p>
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition-colors">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-300 rounded-lg p-6 shadow-lg opacity-75">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-purple-800 mb-2">Compte mis à jour</h3>
                            <p class="text-gray-700 mb-2">Votre compte "Livret A" a été mis à jour avec succès.</p>
                            <p class="text-sm text-gray-500">12/01/2024 14:20</p>
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition-colors">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Filtres -->
        <div class="mt-8">
            <div class="bg-gray-300 rounded-lg p-4">
                <h3 class="text-lg font-bold text-purple-800 mb-4">Filtrer les notifications</h3>
                <form action="{{ route('notifications.index') }}" method="GET" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" id="type" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
                            <option value="">Tous les types</option>
                            <option value="transaction">Transactions</option>
                            <option value="objectif">Objectifs</option>
                            <option value="budget">Budgets</option>
                            <option value="compte">Comptes</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut" id="statut" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
                            <option value="">Tous</option>
                            <option value="non_lu">Non lues</option>
                            <option value="lu">Lues</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-purple-800 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
