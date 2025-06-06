<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - WalletWise</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-400 to-purple-700 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-xl rounded-xl p-10 w-full max-w-md">
        <!-- Titre -->
        <div class="text-center mb-6">
            <h1 class="text-3xl font-extrabold text-purple-800">Créer un compte</h1>
         <p  class="text-gray-600 mt-2">Rejoignez WalletWise dès maintenant</p>
        </div>

        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div class="mb-6">
                <label for="role" class="block text-sm font-medium text-gray-700">Rôle</label>
                <select name="role" id="role" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Utilisateur</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                </select>
            </div>

            <button type="submit"
                class="w-full bg-purple-800 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                S'inscrire
            </button>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-purple-700 hover:underline">Déjà inscrit ? Se connecter</a>
            </div>
        </form>
    </div>
</body>
</html>


