@extends('layouts.app')

@section('title', 'Admin - Utilisateurs')

@section('content')
<div class="flex h-screen bg-purple-400">
    <!-- Sidebar -->
    @include('admin.sidebar')

    <!-- Main content -->
    <div class="flex-1 bg-purple-400 p-8 overflow-auto">
        <h2 class="text-2xl text-white mb-6">Liste des utilisateurs</h2>

        <div class="bg-white p-6 rounded shadow">
            <table class="w-full table-auto">
                <thead>
                    <tr class="text-left text-purple-800 border-b">
                        <th class="pb-2">Nom</th>
                        <th class="pb-2">Email</th>
                        <th class="pb-2">Date d'inscription</th>
                        <th class="pb-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
<tr id="user-{{ $user->id }}" class="border-b hover:bg-purple-100">
    <td class="py-2">{{ $user->name }}</td>
    <td class="py-2">{{ $user->email }}</td>
    <td class="py-2">{{ $user->created_at->format('d/m/Y') }}</td>
    <td class="py-2">
        <a href="{{ route('admin.users.show', ['user' => $user->id]) }}" 
           class="text-blue-600 hover:underline">Voir</a>
        <td class="py-2">
    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="inline" onsubmit="return confirm('Confirmer la suppression ?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-600 hover:underline ml-2 bg-transparent border-none p-0">
            Retirer
        </button>
    </form>
</td>

</tr>
@endforeach
<script>
document.querySelectorAll('.delete-user').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();
        const userId = this.getAttribute('data-id');
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


        if (confirm('Voulez-vous vraiment supprimer cet utilisateur ?')) {
            fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    document.getElementById(`user-${userId}`).remove();
                } else {
                    alert('Erreur lors de la suppression.');
                }
            });
        }
    });
});
</script>


                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
