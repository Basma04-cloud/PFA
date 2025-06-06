// Script pour tester les APIs du dashboard
console.log("🧪 TEST DES APIs DU DASHBOARD")
console.log("=============================\n")

// Fonction pour tester une API
async function testAPI(url, name) {
  try {
    console.log(`🔄 Test de ${name}...`)
    const response = await fetch(url)
    const data = await response.json()

    if (response.ok) {
      console.log(`✅ ${name} - OK:`, data)
      return data
    } else {
      console.log(`❌ ${name} - Erreur:`, response.status, data)
      return null
    }
  } catch (error) {
    console.log(`❌ ${name} - Exception:`, error.message)
    return null
  }
}

// Tester toutes les APIs
async function testAllAPIs() {
  console.log("🚀 Début des tests...\n")

  const results = await Promise.all([
    testAPI("/dashboard/chart-data", "Données mensuelles"),
    testAPI("/dashboard/expenses-data", "Dépenses par catégorie"),
    testAPI("/dashboard/objectifs-data", "Objectifs"),
    testAPI("/dashboard/comptes-data", "Comptes"),
  ])

  console.log("\n📊 RÉSUMÉ DES TESTS:")
  console.log("===================")

  const [monthly, expenses, objectifs, comptes] = results

  console.log(`Données mensuelles: ${monthly ? "✅ OK" : "❌ ERREUR"} (${monthly ? monthly.length : 0} mois)`)
  console.log(`Dépenses: ${expenses ? "✅ OK" : "❌ ERREUR"} (${expenses ? expenses.length : 0} catégories)`)
  console.log(`Objectifs: ${objectifs ? "✅ OK" : "❌ ERREUR"} (${objectifs ? objectifs.length : 0} objectifs)`)
  console.log(`Comptes: ${comptes ? "✅ OK" : "❌ ERREUR"} (${comptes ? comptes.length : 0} comptes)`)

  if (monthly && expenses && objectifs && comptes) {
    console.log("\n🎉 Tous les tests sont passés ! Les graphiques devraient fonctionner.")
  } else {
    console.log("\n⚠️ Certaines APIs ont des problèmes. Vérifiez les erreurs ci-dessus.")
  }
}

// Exécuter les tests
testAllAPIs()

console.log("\n💡 COMMENT UTILISER CE SCRIPT:")
console.log("1. Ouvrez la console de votre navigateur (F12)")
console.log("2. Allez sur votre page dashboard")
console.log("3. Copiez-collez ce script dans la console")
console.log("4. Appuyez sur Entrée pour voir les résultats")
