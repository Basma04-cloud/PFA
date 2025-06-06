// Script pour vérifier la correction et tester

console.log("🔧 VÉRIFICATION DE LA CORRECTION")
console.log("===============================\n")

// Étape 1: Vérifier les tables
async function verifyTables() {
  console.log("📊 ÉTAPE 1: Vérification des tables")
  console.log("-----------------------------------")

  try {
    const response = await fetch("/debug/comptes")
    const data = await response.json()

    console.log("Tables disponibles:")
    console.log("- Table 'compte':", data.table_exists ? "✅ OUI" : "❌ NON")
    console.log("- User connecté:", data.user_authenticated ? "✅ OUI" : "❌ NON")
    console.log("- User ID:", data.user_id)

    return data
  } catch (error) {
    console.log("❌ Erreur:", error.message)
    return null
  }
}

// Étape 2: Tester la création
async function testCreation() {
  console.log("\n🧪 ÉTAPE 2: Test de création")
  console.log("----------------------------")

  try {
    const response = await fetch("/debug/comptes/test-create", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
      },
    })

    const data = await response.json()

    if (data.success) {
      console.log("✅ SUCCÈS ! Compte créé:")
      console.log("- ID:", data.compte.id)
      console.log("- Nom:", data.compte.nom_compte)
      console.log("- User ID:", data.compte.user_id)
      console.log("- Solde:", data.compte.solde)
    } else {
      console.log("❌ ÉCHEC:", data.error)

      if (data.error.includes("foreign key constraint")) {
        console.log("\n💡 La contrainte n'est pas encore corrigée.")
        console.log("Exécutez: php artisan migrate")
      }
    }

    return data
  } catch (error) {
    console.log("❌ Erreur:", error.message)
    return null
  }
}

// Étape 3: Instructions pour la correction
function showInstructions() {
  console.log("\n📋 INSTRUCTIONS POUR CORRIGER")
  console.log("=============================")
  console.log("1. Exécutez cette migration:")
  console.log("   php artisan make:migration fix_foreign_key_reference_to_users")
  console.log("   (Utilisez le code fourni)")
  console.log("")
  console.log("2. Lancez la migration:")
  console.log("   php artisan migrate")
  console.log("")
  console.log("3. Relancez ce script pour tester:")
  console.log("   testAfterFix()")
  console.log("")
  console.log("4. Si ça marche, essayez de créer un compte manuellement")
}

// Fonction principale
async function testAfterFix() {
  console.log("🚀 TEST COMPLET APRÈS CORRECTION")
  console.log("===============================\n")

  const tableData = await verifyTables()
  const creationData = await testCreation()

  console.log("\n📊 RÉSUMÉ")
  console.log("=========")

  if (tableData?.table_exists && tableData?.user_authenticated) {
    console.log("✅ Structure: OK")
  } else {
    console.log("❌ Structure: PROBLÈME")
  }

  if (creationData?.success) {
    console.log("✅ Création: OK")
    console.log("🎉 PROBLÈME RÉSOLU ! Vous pouvez maintenant créer des comptes.")
  } else {
    console.log("❌ Création: ÉCHEC")
    showInstructions()
  }
}

// Lancer le test
testAfterFix()

// Fonction disponible globalement
window.testAfterFix = testAfterFix

console.log("\n💡 COMMANDE DISPONIBLE:")
console.log("testAfterFix() - Relancer le test complet")

