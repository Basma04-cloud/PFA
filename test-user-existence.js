// Script pour vérifier l'existence de l'utilisateur

console.log("🔍 VÉRIFICATION DE L'UTILISATEUR")
console.log("===============================\n")

// Fonction pour vérifier l'utilisateur connecté
async function checkCurrentUser() {
  try {
    const response = await fetch("/debug/comptes")
    const data = await response.json()

    console.log("👤 UTILISATEUR CONNECTÉ:")
    console.log(`ID: ${data.user_id}`)
    console.log(`Authentifié: ${data.user_authenticated}`)

    if (data.user_authenticated && data.user_id) {
      console.log("✅ Utilisateur connecté et ID disponible")
      return data.user_id
    } else {
      console.log("❌ Problème d'authentification")
      return null
    }
  } catch (error) {
    console.log("❌ Erreur lors de la vérification:", error.message)
    return null
  }
}

// Fonction pour tester la création après correction
async function testCreationAfterFix() {
  console.log("\n🧪 TEST APRÈS CORRECTION")
  console.log("------------------------")

  const userId = await checkCurrentUser()

  if (!userId) {
    console.log("❌ Impossible de tester - utilisateur non connecté")
    return
  }

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
      console.log("✅ Création réussie après correction !")
      console.log("Compte créé:", data.compte)
    } else {
      console.log("❌ Création encore échouée:", data.error)
      console.log("User ID utilisé:", data.user_id)
    }
  } catch (error) {
    console.log("❌ Erreur lors du test:", error.message)
  }
}

// Lancer les vérifications
checkCurrentUser().then(testCreationAfterFix)

console.log("\n💡 ÉTAPES À SUIVRE:")
console.log("1. Exécutez la migration de correction")
console.log("2. Relancez ce script pour tester")
console.log("3. Essayez de créer un compte manuellement")
