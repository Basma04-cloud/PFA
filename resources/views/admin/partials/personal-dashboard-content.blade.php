{{-- resources/views/admin/partials/personal-dashboard-content.blade.php --}}

<!-- Cards personnelles (même structure que le dashboard utilisateur) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gray-300 shadow-none rounded-lg p-6">
        <h3 class="text-center text-black font-semibold mb-4">Solde total</h3>
        <p class="text-center text-2xl font-bold text-black">
            {{ number_format($personalData['soldeTotal'] ?? 0, 2, ',', ' ') }} €
        </p>
        <p class="text-center text-sm text-gray-600 mt-2">
            {{ $personalData['nombreComptes'] ?? 0 }} compte(s)
        </p>
    </div>

    <div class="bg-gray-300 shadow-none rounded-lg p-6">
        <h3 class="text-center text-black font-semibold mb-4">Dépenses du mois</h3>
        <p class="text-center text-2xl font-bold text-red-600">
            {{ number_format($personalData['depensesDuMois'] ?? 0, 2, ',', ' ') }} €
        </p>
        <p class="text-center text-sm text-gray-600 mt-2">
            {{ now()->format('F Y') }}
        </p>
    </div>

    <div class="bg-gray-300 shadow-none rounded-lg p-6">
        <h3 class="text-center text-black font-semibold mb-4">Revenus du mois</h3>
        <p class="text-center text-2xl font-bold text-green-600">
            {{ number_format($personalData['revenusDuMois'] ?? 0, 2, ',', ' ') }} €
        </p>
        <p class="text-center text-sm text-gray-600 mt-2">
            {{ now()->format('F Y') }}
        </p>
    </div>
</div>

<!-- Progression des objectifs personnels -->
<div class="bg-white rounded-lg p-6 mb-6">
    <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
        <span class="mr-2">🎯</span> Progression de mes objectifs
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($personalObjectives ?? [] as $objectif)
        <div class="border border-gray-200 rounded-lg p-4">
            <h4 class="font-semibold text-gray-800 mb-2">{{ $objectif->nom }}</h4>
            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                @php
                    $pourcentage = $objectif->montant_cible > 0 ? 
                        min(($objectif->montant_actuel / $objectif->montant_cible) * 100, 100) : 0;
                @endphp
                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $pourcentage }}%"></div>
            </div>
            <div class="flex justify-between text-sm text-gray-600">
                <span>{{ number_format($objectif->montant_actuel, 2, ',', ' ') }} €</span>
                <span>{{ number_format($objectif->montant_cible, 2, ',', ' ') }} €</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                Échéance: {{ $objectif->date_cible ? \Carbon\Carbon::parse($objectif->date_cible)->format('d/m/Y') : 'Non définie' }}
            </p>
        </div>
        @empty
        <div class="col-span-full text-center text-gray-500 py-8">
            <p>Aucun objectif défini</p>
            <a href="{{ route('admin.personal.objectifs') }}" 
               class="mt-2 inline-block bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                Créer un objectif
            </a>
        </div>
        @endforelse
    </div>
</div>

<!-- Dernières transactions personnelles -->
<div class="bg-white rounded-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold text-gray-800 flex items-center">
            <span class="mr-2">💳</span> Mes dernières transactions
        </h3>
        <a href="{{ route('admin.personal.transactions') }}" 
           class="text-purple-600 hover:text-purple-800 text-sm">
            Voir toutes →
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($recentPersonalTransactions ?? [] as $transaction)
                <tr>
                    <td class="px-4 py-2 text-sm font-medium text-gray-900">
                        {{ $transaction->description }}
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-2 text-sm font-medium 
                        {{ $transaction->montant >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ ($transaction->montant >= 0 ? '+' : '') . number_format($transaction->montant, 2, ',', ' ') }} €
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-500">
                        {{ $transaction->categorie }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                        Aucune transaction récente
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>