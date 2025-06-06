@extends('layouts.app')

@section('title', 'Invitations')

@section('content')
<div class="flex h-screen bg-purple-400">
    <!-- Sidebar -->
    @include('admin.sidebar')

    <!-- Main content -->
    <div class="flex-1 bg-purple-100 p-8 overflow-auto">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-3xl font-bold text-purple-800 mb-6">📨 Gérer les invitations</h2>

            <!-- Message de succès -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Formulaire d'envoi -->
            <form method="POST" action="{{ route('admin.invitations.send') }}" class="mb-8">
                @csrf
                <div class="flex flex-col md:flex-row items-center gap-4">
                    <input type="email" name="email" placeholder="Adresse email"
                        class="w-full border border-purple-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        required>
                    <button type="submit"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-2 rounded transition duration-200">
                        ➤ Envoyer
                    </button>
                </div>
            </form>

            <!-- Liste des invitations -->
            <h3 class="text-2xl font-semibold text-purple-700 mb-4">📋 Invitations envoyées</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-purple-200 rounded shadow-sm">
                    <thead class="bg-purple-100 text-purple-800">
                        <tr>
                            <th class="text-left px-4 py-2">Email</th>
                            <th class="text-left px-4 py-2">Envoyée le</th>
                            <th class="text-left px-4 py-2">Expiration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invitations as $invitation)
                            <tr class="border-t border-purple-100 hover:bg-purple-50">
                                <td class="px-4 py-2">{{ $invitation->email }}</td>
                                <td class="px-4 py-2">{{ $invitation->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-2">
                                    {{ $invitation->expires_at ? $invitation->expires_at->format('d/m/Y') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-gray-500">Aucune invitation envoyée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $invitations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
