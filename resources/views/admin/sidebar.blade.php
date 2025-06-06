<div class="w-80 bg-purple-800 text-white p-6 flex flex-col">
    <h1 class="text-3xl font-bold mb-10 text-center">💼 Admin Panel</h1>

    <!-- Section Administration -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <span class="text-lg font-semibold text-purple-200">🔧 Administration</span>
        </div>
        <nav class="space-y-3 ml-4">
            <a href="{{ route('admin.dashboard') }}" 
               class="block text-lg {{ request()->routeIs('admin.dashboard') ? 'text-white font-semibold bg-purple-600 px-3 py-2 rounded' : 'text-purple-200 hover:text-white transition-colors' }}">
                📊 Dashboard Admin
            </a>
            <a href="{{ route('admin.users') }}" 
               class="block text-lg {{ request()->routeIs('admin.users.*') ? 'text-white font-semibold bg-purple-600 px-3 py-2 rounded' : 'text-purple-200 hover:text-white transition-colors' }}">
                👥 Utilisateurs
            </a>
            <a href="{{ route('admin.statistics') }}" 
               class="block text-lg {{ request()->routeIs('admin.statistics') ? 'text-white font-semibold bg-purple-600 px-3 py-2 rounded' : 'text-purple-200 hover:text-white transition-colors' }}">
                📈 Statistiques
            </a>
            <a href="{{ route('admin.invitations.index') }}" 
               class="block text-lg {{ request()->routeIs('admin.invitations.index') ? 'text-white font-semibold bg-purple-600 px-3 py-2 rounded' : 'text-purple-200 hover:text-white transition-colors' }}">
                📋 Invitations
            </a>


        
        </nav>
    </div>

    <div class="border-t border-purple-600 mb-6"></div>

    <!-- Section Personnel -->
    <div class="flex-1">
        <div class="flex items-center mb-4">
            <span class="text-lg font-semibold text-purple-200">👤 Personnel</span>
        </div>
        <nav class="space-y-3 ml-4">
            <a href="{{ route('dashboard') }}" 
               class="block text-lg {{ request()->routeIs('dashboard') ? 'text-white font-semibold bg-green-600 px-3 py-2 rounded' : 'text-purple-200 hover:text-white transition-colors' }}">
                🏠 Mon Dashboard Personnel
            </a>
        </nav>
    </div>

    <!-- Déconnexion -->
    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button type="submit" class="w-full text-lg bg-red-600 hover:bg-red-700 px-4 py-2 rounded transition-colors">
            🚪 Déconnexion
        </button>
    </form>
</div>

