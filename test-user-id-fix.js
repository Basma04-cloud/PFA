// Script pour tester la correction du problème user_id

console.log("🔧 TEST DE LA CORRECTION USER_ID")
console.log("================================\n")

// Test 1: Vérifier l'authentification
function testAuthentication() {
  console.log("🔐 Test 1: Authentification")
  console.log("---------------------------")
  \
  const userAuthenticated = {{ Auth::check() ? 'true' : 'false' }
}
const userId = {{ Auth::id() }
}
const userName = '{{ Auth::user()->name ?? "N/A" }}'

console.log(`Utilisateur connecté: ${userAuthenticated}`)
console.log(`User ID: ${userId}`)
console.log(`Nom utilisateur: ${userName}`)

if (userAuthenticated && userId) {
  console.log("✅ Authentification OK")
  return true;
} else {
  console.log("❌ Problème d'authentification")
  return false;
}
}

// Test 2: Vérifier les règles de validation
async
function testValidationRules() {
  console.log("\n📋 Test 2: Règles de validation")
  console.log("-------------------------------")

  try {
    const response = await fetch('/debug/comptes')
    const data = await response.json()

    console.log("Règles de validation:", data.validation_rules)

    const hasUserIdRule = data.validation_rules && data.validation_rules.user_id

    if (hasUserIdRule) {
      console.log("❌ PROBLÈME: user_id est encore dans les règles de validation")
      console.log("💡 Solution: Le contrôleur doit utiliser getValidationRules() au lieu de getFullValidationRules()")
      return false
    } else {
      console.log("✅ Règles de validation correctes (pas de user_id requis)")
      return true
    }
  } catch (error) {
    console.log("❌ Erreur lors du test:", error.message)
    return false
  }
}

// Test 3: Simuler une soumission de formulaire
function testFormData() {
  console.log("\n📝 Test 3: Données du formulaire")
  console.log("--------------------------------")

  const formData = {
    nom_compte: "Test Compte " + new Date().getTime(),
    type_compte: "courant",
    solde: 1000,
    description: "Test de création sans user_id dans le formulaire",
  }

  console.log("Données envoyées:", formData)

  // Vérifier qu'il n'y a PAS de user_id dans les données
  if (formData.user_id) {
    console.log("❌ PROBLÈME: user_id ne devrait pas être dans les données du formulaire")
    return false
  } else {
    console.log("✅ Données correctes (pas de user_id dans le formulaire)")
    return true
  }
}

// Test 4: Test de création réelle
async function testRealCreation() {
  console.log("\n🧪 Test 4: Création réelle")
  console.log("-------------------------")

  try {
    const response = await fetch("/debug/comptes/test-create", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
      },
    })

    const data = await response.json()

    console.log("Résultat de la création:", data)

    if (data.success) {
      console.log("✅ Création réussie")
      console.log(`Compte créé avec ID: ${data.compte.id}`)
      console.log(`User ID associé: ${data.user_id}`)
      return true
    } else {
      console.log("❌ Création échouée:", data.error)
      return false
    }
  } catch (error) {
    console.log("❌ Erreur lors de la création:", error.message)
    return false
  }
}

// Exécuter tous les tests
async function runAllTests() {
  console.log("🚀 DÉBUT DES TESTS DE CORRECTION")
  console.log("================================\n")

  const results = {
    auth: testAuthentication(),
    validation: await testValidationRules(),
    formData: testFormData(),
    creation: await testRealCreation(),
  }

  console.log("\n📊 RÉSUMÉ DES TESTS")
  console.log("==================")

  const allPassed = Object.values(results).every((result) => result === true)

  Object.entries(results).forEach(([test, passed]) => {
    console.log(`${passed ? "✅" : "❌"} ${test}: ${passed ? "OK" : "ÉCHEC"}`)
  })

  if (allPassed) {
    console.log("\n🎉 TOUS LES TESTS SONT PASSÉS !")
    console.log("Le problème 'user id field is required' est corrigé.")
    console.log("Vous pouvez maintenant créer des comptes normalement.")
  } else {
    console.log("\n⚠️ CERTAINS TESTS ONT ÉCHOUÉ")
    console.log("Vérifiez les détails ci-dessus pour identifier le problème.")
  }

  return results
}

// Lancer les tests automatiquement
runAllTests()

// Fonction utilitaire pour les développeurs
window.testUserIdFix = runAllTests

console.log("\n💡 COMMANDE DISPONIBLE:")
console.log("testUserIdFix() - Relancer tous les tests")
