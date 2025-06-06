<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - WalletWise</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-400 to-purple-700 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-xl rounded-xl p-10 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-purple-800">Bienvenue sur <span class="text-purple-600">WalletWise</span></h1>
            <p class="text-gray-600 mt-2">Connectez-vous pour accéder à votre espace</p>
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

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" autocomplete="email" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input type="password" name="password" id="password" autocomplete="current-password" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div class="flex items-center justify-between mb-4">
                <button type="submit"
                    class="w-full bg-purple-800 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                    Se connecter
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('register') }}" class="text-sm text-purple-700 hover:underline">Pas encore de compte ? S'inscrire</a>
            </div>
        </form>
    </div>
</body>
</html>

