@extends('layouts.app')

@section('title', 'Mes comptes')

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
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl text-white font-bold">Mes comptes</h2>
            <a href="{{ route('comptes.create') }}" class="bg-gray-300 text-black hover:bg-white rounded-full px-4 py-1 transition-colors text-sm">
                + Ajouter un compte
            </a>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
        @endif

        <!-- Comptes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($comptes as $compte)
            <div class="bg-gray-200 rounded-lg overflow-hidden">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="w-10 h-10 {{ $compte->icon_color }} rounded mr-3 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                {!! $compte->icon_svg !!}
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold">{{ $compte->nom }}</h3>
                            <p class="text-sm text-gray-600">{{ $compte->type }}</p>
                            <p class="text-lg font-bold mt-2 {{ $compte->solde >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($compte->solde, 2, ',', ' ') }} MAD
                            </p>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-300 flex">
                    <a href="{{ route('comptes.edit', $compte->id) }}" class="flex-1 text-center py-2 hover:bg-gray-300 transition-colors">Modifier</a>
                    <form method="POST" action="{{ route('comptes.destroy', $compte->id) }}" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-center py-2 hover:bg-gray-300 transition-colors" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce compte ?')">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-2 text-center py-8">
                <p class="text-white text-lg mb-4">Vous n'avez pas encore de comptes.</p>
                <a href="{{ route('comptes.create') }}" class="bg-gray-300 text-black hover:bg-white rounded-full px-6 py-2 transition-colors">
                    Créer votre premier compte
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
