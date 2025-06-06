@extends('layouts.app')

@section('title', 'Nouveau Compte')

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
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center mb-8">
                <a href="{{ route('comptes.index') }}" class="text-white hover:text-gray-300 mr-4">← Retour</a>
                <h2 class="text-3xl text-white font-bold">Nouveau Compte</h2>
            </div>

            <!-- Debug info CORRIGÉ -->
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                <p><strong>🔍 Debug Type de Compte:</strong></p>
                <p>✅ Utilisateur: {{ Auth::user()->name }} (ID: {{ Auth::id() }})</p>
                <div class="mt-2 space-x-2">
                    <button onclick="diagnoseProblemFixed()" class="bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                        Diagnostiquer (Corrigé)
                    </button>
                    <button onclick="checkDatabaseDataFixed()" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">
                        Vérifier la base
                    </button>
                    <button onclick="testFormValues()" class="bg-green-600 text-white px-3 py-1 rounded text-sm">
                        Tester le formulaire
                    </button>
                    <button onclick="createTestAccount()" class="bg-purple-600 text-white px-3 py-1 rounded text-sm">
                        Créer compte test
                    </button>
                </div>
            </div>

            <div class="bg-gray-300 rounded-lg p-8">
                @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <h4 class="font-bold mb-2">Erreurs détectées :</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
                @endif

                <form method="POST" action="{{ route('comptes.store') }}" id="compteForm">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-6">
                            <label for="nom_compte" class="block text-purple-800 text-sm font-bold mb-2">Nom du compte *</label>
                            <input type="text" name="nom_compte" id="nom_compte" value="{{ old('nom_compte') }}" 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" 
                                   placeholder="Ex: Compte principal, Épargne vacances..." required>
                        </div>

                        <div class="mb-6">
                            <label for="type_compte" class="block text-purple-800 text-sm font-bold mb-2">Type de compte *</label>
                            <select name="type_compte" id="type_compte" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" required>
                                <option value="">Sélectionner un type</option>
                                <option value="courant" {{ old('type_compte') == 'courant' ? 'selected' : '' }}>Compte Courant</option>
                                <option value="epargne" {{ old('type_compte') == 'epargne' ? 'selected' : '' }}>Compte Épargne</option>
                                <option value="credit" {{ old('type_compte') == 'credit' ? 'selected' : '' }}>Compte Crédit</option>
                                <option value="investissement" {{ old('type_compte') == 'investissement' ? 'selected' : '' }}>Compte Investissement</option>
                            </select>
                            <!-- Debug du type sélectionné -->
                            <p class="text-xs text-blue-600 mt-1" id="type-debug">Type sélectionné: <span id="selected-type">aucun</span></p>
                        </div>

                        <div class="mb-6 md:col-span-2">
                            <label for="solde" class="block text-purple-800 text-sm font-bold mb-2">Solde initial</label>
                            <input type="number" step="0.01" min="0" name="solde" id="solde" value="{{ old('solde', '0') }}" 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
                        </div>
                    </div>

                    @if(isset($hasDescription) && $hasDescription)
                    <div class="mb-6">
                        <label for="description" class="block text-purple-800 text-sm font-bold mb-2">Description</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500" 
                                  placeholder="Description optionnelle du compte...">{{ old('description') }}</textarea>
                    </div>
                    @endif

                    <!-- Aperçu du compte avec type détaillé -->
                    <div class="mb-6 p-4 bg-purple-100 rounded-lg" id="apercu" style="display: none;">
                        <h3 class="text-purple-800 font-bold mb-2">Aperçu de votre compte :</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-semibold">Nom :</span> <span id="apercu-nom">-</span>
                            </div>
                            <div>
                                <span class="font-semibold">Type (valeur) :</span> <span id="apercu-type-value" class="font-mono bg-gray-200 px-1">-</span>
                            </div>
                            <div>
                                <span class="font-semibold">Type (affiché) :</span> <span id="apercu-type-display">-</span>
                            </div>
                            <div>
                                <span class="font-semibold">Solde :</span> <span id="apercu-solde">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('comptes.index') }}" class="px-6 py-2 border border-gray-400 rounded-lg hover:bg-gray-100 transition-colors">
                            Annuler
                        </a>
                        <button type="submit" class="px-6 py-2 bg-purple-800 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            Créer le compte
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Mapping des types pour l'affichage
const typeMapping = {
    'courant': 'Compte Courant',
    'epargne': 'Compte Épargne',
    'credit': 'Compte Crédit',
    'investissement': 'Compte Investissement'
};

// Fonction pour mettre à jour l'aperçu avec debug du type
function updateApercu() {
    const nom = document.getElementById('nom_compte').value;
    const typeValue = document.getElementById('type_compte').value;
    const solde = parseFloat(document.getElementById('solde').value) || 0;
    
    // Mettre à jour le debug du type
    document.getElementById('selected-type').textContent = typeValue || 'aucun';
    
    if (nom || typeValue) {
        document.getElementById('apercu').style.display = 'block';
        
        document.getElementById('apercu-nom').textContent = nom || '-';
        document.getElementById('apercu-type-value').textContent = typeValue || '-';
        document.getElementById('apercu-type-display').textContent = typeMapping[typeValue] || '-';
        document.getElementById('apercu-solde').textContent = solde.toLocaleString('fr-FR', {minimumFractionDigits: 2}) + ' MAD';
    } else {
        document.getElementById('apercu').style.display = 'none';
    }
}

// Écouter les changements dans les champs
['nom_compte', 'type_compte', 'solde'].forEach(id => {
    const element = document.getElementById(id);
    if (element) {
        element.addEventListener('input', updateApercu);
        element.addEventListener('change', updateApercu);
    }
});

// Validation côté client avec logging détaillé
document.getElementById('compteForm').addEventListener('submit', function(e) {
    const nomCompte = document.getElementById('nom_compte').value.trim();
    const typeCompte = document.getElementById('type_compte').value;
    const solde = parseFloat(document.getElementById('solde').value) || 0;
    
    console.log('🔍 SOUMISSION DU FORMULAIRE:', {
        nom_compte: nomCompte,
        type_compte: typeCompte,
        type_display: typeMapping[typeCompte],
        solde: solde,
        timestamp: new Date().toISOString()
    });
    
    // Vérification explicite du type
    if (!typeCompte || !['courant', 'epargne', 'credit', 'investissement'].includes(typeCompte)) {
        e.preventDefault();
        alert(`Type de compte invalide: "${typeCompte}"\nTypes autorisés: courant, epargne, credit, investissement`);
        return false;
    }
    
    console.log('✅ Validation réussie, type confirmé:', typeCompte);
});

// FONCTIONS DE DEBUG CORRIGÉES

// Fonction pour diagnostiquer avec routes alternatives
async function diagnoseProblemFixed() {
    console.log("🚀 DIAGNOSTIC CORRIGÉ DU PROBLÈME DE TYPE");
    
    // Essayer plusieurs routes de debug
    const debugRoutes = [
        '/debug/comptes/simple',
        '/debug/comptes',
        '/debug/comptes/routes'
    ];
    
    for (const route of debugRoutes) {
        try {
            console.log(`🔄 Tentative avec ${route}...`);
            
            const response = await fetch(route, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            console.log(`Status: ${response.status}`);
            console.log(`Content-Type: ${response.headers.get('content-type')}`);
            
            if (response.ok && response.headers.get('content-type')?.includes('application/json')) {
                const data = await response.json();
                console.log(`✅ Succès avec ${route}:`, data);
                
                if (data.comptes && data.comptes.length > 0) {
                    console.log("📊 COMPTES TROUVÉS:");
                    data.comptes.forEach((compte, index) => {
                        console.log(`${index + 1}. ${compte.nom}`);
                        console.log(`   Type: "${compte.type}"`);
                        console.log(`   Créé: ${compte.created}`);
                    });
                    
                    const dernier = data.comptes[0];
                    alert(`✅ Diagnostic réussi!\n\nDernier compte:\nNom: ${dernier.nom}\nType en base: "${dernier.type}"\nSolde: ${dernier.solde}MAD`);
                } else if (data.user_comptes && data.user_comptes.length > 0) {
                    console.log("📊 COMPTES TROUVÉS:");
                    data.user_comptes.forEach((compte, index) => {
                        console.log(`${index + 1}. ${compte.nom_compte}`);
                        console.log(`   Type en base: "${compte.type_compte}"`);
                        console.log(`   Type formatté: "${compte.type_formatte}"`);
                    });
                    
                    const dernier = data.user_comptes[0];
                    alert(`✅ Diagnostic réussi!\n\nDernier compte:\nNom: ${dernier.nom_compte}\nType en base: "${dernier.type_compte}"\nType affiché: "${dernier.type_formatte}"`);
                } else {
                    alert("ℹ️ Aucun compte trouvé. Créez d'abord un compte pour tester.");
                }
                
                return; // Succès, on s'arrête ici
            }
        } catch (error) {
            console.log(`❌ Erreur avec ${route}:`, error.message);
        }
    }
    
    // Si toutes les routes ont échoué
    alert("❌ Toutes les routes de debug ont échoué.\nVérifiez que vous êtes connecté et que les routes existent.");
}

// Fonction pour vérifier la base avec méthode alternative
async function checkDatabaseDataFixed() {
    try {
        console.log("🔍 Vérification alternative de la base...");
        
        // Essayer d'abord la route simple
        const response = await fetch('/debug/comptes/simple', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            console.log("📊 DONNÉES DE LA BASE:", data);
            
            if (data.comptes && data.comptes.length > 0) {
                console.log("✅ Comptes trouvés:");
                data.comptes.forEach(compte => {
                    console.log(`- ${compte.nom}: type="${compte.type}", solde=${compte.solde}MAD`);
                });
                
                alert(`✅ Base de données accessible!\n${data.comptes.length} compte(s) trouvé(s)`);
            } else {
                alert("ℹ️ Base accessible mais aucun compte trouvé");
            }
        } else {
            throw new Error(`HTTP ${response.status}`);
        }
    } catch (error) {
        console.log("❌ Erreur:", error.message);
        alert("❌ Impossible d'accéder à la base de données: " + error.message);
    }
}

// Fonction pour tester les valeurs du formulaire
function testFormValues() {
    const form = document.getElementById('compteForm');
    const formData = new FormData(form);
    
    console.log('📝 VALEURS ACTUELLES DU FORMULAIRE:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}: "${value}"`);
    }
    
    const typeSelect = document.getElementById('type_compte');
    console.log('🔍 DÉTAILS DU SELECT TYPE:');
    console.log(`  Valeur: "${typeSelect.value}"`);
    console.log(`  Index sélectionné: ${typeSelect.selectedIndex}`);
    console.log(`  Option sélectionnée: "${typeSelect.options[typeSelect.selectedIndex]?.text}"`);
    
    alert(`Type sélectionné: "${typeSelect.value}"\nAffichage: "${typeMapping[typeSelect.value] || 'Inconnu'}"`);
}

// Fonction pour créer un compte de test
async function createTestAccount() {
    console.log("🧪 Création d'un compte de test...");
    
    try {
        const formData = new FormData();
        formData.append('nom_compte', 'Test Debug ' + new Date().getTime());
        formData.append('type_compte', 'courant');
        formData.append('solde', '1000');
        formData.append('description', 'Compte créé pour debug du type');
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            formData.append('_token', csrfToken);
        }
        
        console.log("Données à envoyer:");
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}: "${value}"`);
        }
        
        const response = await fetch('/comptes', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        console.log(`Status: ${response.status}`);
        
        if (response.ok) {
            console.log("✅ Compte créé avec succès");
            alert("✅ Compte de test créé avec succès!\nVérifiez maintenant la liste des comptes.");
            
            // Recharger la page pour voir le résultat
            setTimeout(() => {
                window.location.href = '/comptes';
            }, 2000);
        } else {
            const text = await response.text();
            console.log("❌ Erreur lors de la création:", text.substring(0, 300));
            alert("❌ Erreur lors de la création du compte de test");
        }
        
    } catch (error) {
        console.log("❌ Exception:", error.message);
        alert("❌ Exception: " + error.message);
    }
}

// Initialisation
console.log('🔧 Page de création chargée avec debug corrigé');
console.log('📋 Types disponibles:', typeMapping);
</script>
@endsection





