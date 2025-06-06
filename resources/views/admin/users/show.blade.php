@extends('layouts.app')

@section('title', 'Détails Utilisateur')

@section('content')
<div class="flex h-screen bg-purple-400 font-sans">
    <!-- Sidebar -->
    @include('admin.sidebar')

    <!-- Main content -->
    <div class="flex-1 bg-purple-100 p-10 overflow-auto space-y-8">
        <!-- Carte utilisateur -->
        <div class="bg-white p-8 rounded-xl shadow-xl transition duration-300 hover:shadow-2xl">
            <h1 class="text-4xl font-extrabold text-purple-800 mb-6 flex items-center gap-2">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Détails de l'utilisateur
            </h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700 text-lg">
                <p><strong>Nom :</strong> {{ $user->name }}</p>
                <p><strong>Email :</strong> {{ $user->email }}</p>
                <p><strong>Rôle :</strong> {{ $user->role }}</p>
                <p><strong>Inscrit le :</strong> {{ $user->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        <!-- Transactions -->
        <div class="bg-white p-8 rounded-xl shadow-xl transition duration-300 hover:shadow-2xl">
            <h2 class="text-3xl font-semibold text-purple-700 mb-4">💳 Transactions</h2>
            <ul class="list-disc pl-6 text-gray-700 space-y-2">
                @forelse($user->transactions as $transaction)
                    <li class="{{ $transaction->montant < 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $transaction->montant < 0 ? '-' : '+' }}{{ abs($transaction->montant) }} MAD - Crée le {{ $transaction->date->format('d/m/Y') }}
                    </li>
                @empty
                    <li class="text-gray-500 italic">Aucune transaction enregistrée.</li>
                @endforelse
            </ul>
        </div>

        <!-- Comptes -->
        <div class="bg-white p-8 rounded-xl shadow-xl transition duration-300 hover:shadow-2xl">
            <h2 class="text-3xl font-semibold text-purple-700 mb-4">🏦 Comptes</h2>
            <ul class="list-disc pl-6 text-gray-700 space-y-2">
                @forelse($user->comptes as $compte)
                    <li>{{ $compte->nom_compte }} ({{ $compte->type_compte }})  :<strong> {{ number_format($compte->solde, 2) }} MAD</strong></li>
                @empty
                    <li class="text-gray-500 italic">Aucun compte enregistré.</li>
                @endforelse
            </ul>
        </div>

        <!-- Objectifs -->
        <div class="bg-white p-8 rounded-xl shadow-xl transition duration-300 hover:shadow-2xl">
            <h2 class="text-3xl font-semibold text-purple-700 mb-4">🎯 Objectifs</h2>
            <ul class="list-disc pl-6 text-gray-700 space-y-2">
                @forelse($user->objectifs as $objectif)
                    <li>
                        {{ $objectif->nom }} : 
                        <span class="font-medium">  {{ number_format($objectif->montant_atteint, 2) }} €</span>
                        @if($objectif->sous_objectifs)
                            <ul class="list-disc pl-6 text-sm text-gray-600 mt-1 space-y-1">
                                @foreach($objectif->sous_objectifs as $sous)
                                    <li>{{ $sous->nom }} : {{ number_format($sous->montant, 2) }}MAD</li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @empty
                    <li class="text-gray-500 italic">Aucun objectif enregistré.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
