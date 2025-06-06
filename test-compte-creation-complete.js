// Script de test complet pour la création de compte

console.log("🧪 TEST COMPLET DE CRÉATION DE COMPTE")
console.log("====================================\n")

// Test 1: Vérifier la structure de la table
async function testTableStructure() {
  console.log("📊 Test 1: Structure de la table")
  console.log("--------------------------------")

  try {
    const response = await fetch("/debug/comptes")
    const data = await response.json()

    console.log("✅ Réponse reçue:", data)

    if (data.table_exists) {
      console.log("✅ Table 'compte' existe")
      console.log("Colonnes:", data.table_structure.map((col) => col.Field).join(", "))
      console.log("Champs fillable:", data.fillable_fields.join(", "))
    } else {
      console.log("❌ Table 'compte' n'existe pas !")
    }

    return data
  } catch (error) {
    console.log("❌ Erreur:", error.message)
    return null
  }
}

// Test 2: Tester la création automatique
async function testAutoCreate() {
  console.log("\n🤖 Test 2: Création automatique")
  console.log("-------------------------------")

  try {
    const response = await fetch("/debug/comptes/test-create", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "no-token",
      },
    })

    const data = await response.json()

    if (data.success) {
      console.log("✅ Création automatique réussie")
      console.log("Compte créé:", data.compte)
    } else {
      console.log("❌ Création automatique échouée:", data.error)
    }

    return data
  } catch (error) {
    console.log("❌ Erreur:", error.message)
    return null
  }
}

// Test 3: Simuler une soumission de formulaire
function testFormSubmission() {
  console.log("\n📝 Test 3: Simulation de formulaire")
  console.log("-----------------------------------")

  const formData = {
    nom_compte: "Test Compte " + new Date().getTime(),
    type_compte: "courant",
    solde: 1000,
    description: "Compte de test créé automatiquement",
  }

  console.log("Données du formulaire:", formData)

  // Vérifier la validation côté client
  if (!formData.nom_compte) {
    console.log("❌ Nom du compte manquant")
    return false
  }

  if (!formData.type_compte) {
    console.log("❌ Type de compte manquant")
    return false
  }

  if (formData.solde < 0) {
    console.log("❌ Solde négatif")
    return false
  }

  console.log("✅ Validation côté client réussie")
  return true
}

// Test 4: Vérifier les routes
async function testRoutes() {
  console.log("\n🛣️  Test 4: Vérification des routes")
  console.log("----------------------------------")

  const routes = [
    { url: "/comptes", name: "Index des comptes" },
    { url: "/comptes/create", name: "Formulaire de création" },
    { url: "/debug/comptes", name: "Debug des comptes" },
  ]

  for (const route of routes) {
    try {
      const response = await fetch(route.url)
      if (response.ok) {
        console.log(`✅ ${route.name} - OK (${response.status})`)
      } else {
        console.log(`❌ ${route.name} - Erreur ${response.status}`)
      }
    } catch (error) {
      console.log(`❌ ${route.name} - Exception: ${error.message}`)
    }
  }
}

// Exécuter tous les tests
async function runAllTests() {
  console.log("🚀 DÉBUT DES TESTS")
  console.log("==================\n")

  const results = {
    structure: await testTableStructure(),
    autoCreate: await testAutoCreate(),
    formValidation: testFormSubmission(),
    routes: await testRoutes(),
  }

  console.log("\n📋 RÉSUMÉ")
  console.log("=========")

  if (results.structure?.table_exists) {
    console.log("✅ Structure de table: OK")
  } else {
    console.log("❌ Structure de table: PROBLÈME")
    console.log("💡 Solution: Exécutez 'php artisan migrate'")
  }

  if (results.autoCreate?.success) {
    console.log("✅ Création automatique: OK")
  } else {
    console.log("❌ Création automatique: PROBLÈME")
    console.log("💡 Vérifiez les logs Laravel")
  }

  if (results.formValidation) {
    console.log("✅ Validation formulaire: OK")
  } else {
    console.log("❌ Validation formulaire: PROBLÈME")
  }

  console.log("\n🎯 PROCHAINES ÉTAPES:")
  if (!results.structure?.table_exists) {
    console.log("1. Exécutez: php artisan migrate")
  }
  if (!results.autoCreate?.success) {
    console.log("2. Vérifiez les logs: storage/logs/laravel.log")
  }
  console.log("3. Essayez de créer un compte manuellement")

  return results
}

// Lancer les tests
runAllTests()

// Fonction utilitaire pour les développeurs
window.testCompteCreation = runAllTests
window.debugCompte = testTableStructure

console.log("\n💡 COMMANDES DISPONIBLES:")
console.log("testCompteCreation() - Lancer tous les tests")
console.log("debugCompte() - Vérifier la structure de table")
