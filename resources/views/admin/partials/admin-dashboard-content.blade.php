{{-- resources/views/admin/partials/admin-dashboard-content.blade.php --}}

<!-- Cards administrateur -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gray-300 shadow-none rounded-lg p-6">
        <h3 class="text-center text-black font-semibold mb-4">Utilisateurs enregistrés</h3>
        <p class="text-center text-2xl font-bold text-blue-600">
            {{ $totalUsers ?? 6 }}
        </p>
        <p class="text-center text-sm text-gray-600 mt-2">utilisateurs actifs</p>
    </div>

    <div class="bg-gray-300 shadow-none rounded-lg p-6">
        <h3 class="text-center text-black font-semibold mb-4">Revenus totaux</h3>
        <p class="text-center text-2xl font-bold text-green-600">
            {{ number_format($totalRevenues ?? 10000, 2, ',', ' ') }} MAD
        </p>
        <p class="text-center text-sm text-gray-600 mt-2">tous utilisateurs</p>
    </div>

    <div class="bg-gray-300 shadow-none rounded-lg p-6">
        <h3 class="text-center text-black font-semibold mb-4">Dépenses totales</h3>
        <p class="text-center text-2xl font-bold text-red-600">
            {{ number_format($totalExpenses ?? 95000, 2, ',', ' ') }} MAD
        </p>
        <p class="text-center text-sm text-gray-600 mt-2">tous utilisateurs</p>
    </div>
</div>

<!-- Section utilisateurs récents -->
<div class="bg-white rounded-lg p-6 mb-6">
    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
        <span class="mr-2">👥</span> Derniers utilisateurs inscrits
    </h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inscrit le</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($recentUsers ?? [] as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $user->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                            {{ $user->role === 'admin' ? 'Admin' : 'Utilisateur' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.users.show', ['user' => $user->id]) }}" 
                            class="text-purple-600 hover:text-purple-900">Voir détails</a>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        Aucun utilisateur récent
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Section objectifs populaires -->
<div class="bg-white rounded-lg p-6">
    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
        <span class="mr-2">🎯</span> Objectifs les plus populaires
    </h3>
    <div class="space-y-3">
        @forelse($popularObjectives ?? [] as $objective)
        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
            <span class="font-medium text-gray-700">{{ $objective->name }}</span>
            <span class="bg-purple-600 text-white px-3 py-1 rounded-full text-sm">
                {{ $objective->users_count }} utilisateur{{ $objective->users_count > 1 ? 's' : '' }}
            </span>
        </div>
        @empty
        <p class="text-gray-500 text-center py-4">Aucun objectif enregistré</p>
        @endforelse
    </div>
</div>
