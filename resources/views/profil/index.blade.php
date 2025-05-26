@extends('layouts.app')

@section('title', 'Profil')

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
            <a href="{{ route('notifications.index') }}" class="block text-xl hover:text-gray-300 transition-colors">Notifications</a>
            <a href="{{ route('profil.index') }}" class="block text-xl font-semibold text-purple-200">Profil</a>
        </nav>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="block text-xl mt-auto hover:text-gray-300">Déconnexion</button>
        </form>
    </div>

    <!-- Main content -->
    <div class="flex-1 bg-purple-400 p-8 overflow-auto">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl text-white font-bold mb-8">Mon Profil</h2>

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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Informations personnelles -->
                <div class="bg-gray-300 rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-purple-800">Informations personnelles</h3>
                        <a href="{{ route('profil.edit') }}" class="bg-purple-800 text-white px-4 py-2 rounded hover:bg-purple-700 transition-colors">
                            Modifier
                        </a>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nom</label>
                            <p class="text-lg text-gray-900">{{ Auth::user()->name ?? 'Non défini' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="text-lg text-gray-900">{{ Auth::user()->email ?? 'Non défini' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Membre depuis</label>
                            <p class="text-lg text-gray-900">
                                {{ Auth::user() && Auth::user()->created_at ? Auth::user()->created_at->format('d/m/Y') : 'Non défini' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Sécurité -->
                <div class="bg-gray-300 rounded-lg p-6">
                    <h3 class="text-xl font-bold text-purple-800 mb-6">Sécurité</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                            <a href="{{ route('profil.change-password') }}" class="bg-purple-800 text-white px-4 py-2 rounded hover:bg-purple-700 transition-colors inline-block">
                                Changer le mot de passe
                            </a>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Dernière connexion</label>
                            <p class="text-lg text-gray-900">Aujourd'hui</p>
                        </div>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="bg-gray-300 rounded-lg p-6">
                    <h3 class="text-xl font-bold text-purple-800 mb-6">Statistiques</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Nombre de comptes</span>
                            <span class="font-semibold">{{ $stats['comptes'] ?? '3' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Transactions ce mois</span>
                            <span class="font-semibold">{{ $stats['transactions'] ?? '24' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Objectifs actifs</span>
                            <span class="font-semibold">{{ $stats['objectifs'] ?? '2' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Notifications non lues</span>
                            <span class="font-semibold text-red-600">{{ $stats['notifications_non_lues'] ?? '0' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Préférences -->
                <div class="bg-gray-300 rounded-lg p-6">
                    <h3 class="text-xl font-bold text-purple-800 mb-6">Préférences</h3>
                    
                    <form method="POST" action="{{ route('profil.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Notifications par email</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="email_notifications" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Alertes de budget</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="budget_alerts" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Rappels d'objectifs</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="goal_reminders" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="bg-purple-800 text-white px-4 py-2 rounded hover:bg-purple-700 transition-colors">
                                Sauvegarder les préférences
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
