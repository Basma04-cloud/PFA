import { Chart } from "@/components/ui/chart"
// Script de diagnostic étape par étape pour les graphiques

console.log("🔍 DIAGNOSTIC COMPLET DES GRAPHIQUES")
console.log("===================================\n")

// Étape 1: Vérifier que Chart.js est chargé
console.log("📊 ÉTAPE 1: Vérification de Chart.js")
console.log("------------------------------------")
if (typeof Chart !== "undefined") {
  console.log("✅ Chart.js est chargé, version:", Chart.version)
} else {
  console.log("❌ Chart.js n'est pas chargé !")
  console.log("💡 Solution: Vérifiez que le CDN Chart.js est accessible")
}

// Étape 2: Vérifier les éléments canvas
console.log("\n🎨 ÉTAPE 2: Vérification des éléments canvas")
console.log("---------------------------------------------")
const canvasIds = ["monthlyChart", "expensesChart", "objectifsChart", "comptesChart", "yearlyChart"]
canvasIds.forEach((id) => {
  const element = document.getElementById(id)
  if (element) {
    console.log(`✅ Canvas ${id} trouvé`)
  } else {
    console.log(`❌ Canvas ${id} non trouvé !`)
  }
})

// Étape 3: Tester les routes API
console.log("\n🔗 ÉTAPE 3: Test des routes API")
console.log("-------------------------------")

async function testRoute(url, name) {
  try {
    console.log(`🔄 Test ${name}...`)
    const response = await fetch(url)

    if (response.ok) {
      const data = await response.json()
      console.log(`✅ ${name} - OK (${data.length || "N/A"} éléments):`, data)
      return { success: true, data }
    } else {
      console.log(`❌ ${name} - Erreur ${response.status}`)
      const errorText = await response.text()
      console.log("Détails:", errorText)
      return { success: false, error: response.status }
    }
  } catch (error) {
    console.log(`❌ ${name} - Exception:`, error.message)
    return { success: false, error: error.message }
  }
}

async function runAllTests() {
  const tests = [
    { url: "/dashboard/chart-data", name: "Données mensuelles" },
    { url: "/dashboard/expenses-data", name: "Dépenses par catégorie" },
    { url: "/dashboard/objectifs-data", name: "Objectifs" },
    { url: "/dashboard/comptes-data", name: "Comptes" },
  ]

  const results = []
  for (const test of tests) {
    const result = await testRoute(test.url, test.name)
    results.push({ ...test, ...result })
  }

  console.log("\n📋 RÉSUMÉ DES TESTS:")
  console.log("===================")

  const successful = results.filter((r) => r.success).length
  const total = results.length

  console.log(`Réussis: ${successful}/${total}`)

  if (successful === total) {
    console.log("🎉 Tous les tests sont passés ! Le problème vient peut-être d'ailleurs.")
    console.log("\n🔧 ÉTAPES SUIVANTES:")
    console.log("1. Vérifiez la console pour d'autres erreurs")
    console.log("2. Vérifiez que les fonctions de création de graphiques sont appelées")
    console.log("3. Vérifiez les permissions CSRF si nécessaire")
  } else {
    console.log("⚠️ Certains tests ont échoué. Vérifiez:")
    console.log("1. Les routes sont-elles définies dans web.php ?")
    console.log("2. Le contrôleur DashboardController existe-t-il ?")
    console.log("3. L'utilisateur est-il bien connecté ?")
    console.log("4. Y a-t-il des erreurs dans les logs Laravel ?")
  }

  return results
}

// Étape 4: Vérifier l'authentification
console.log("\n🔐 ÉTAPE 4: Vérification de l'authentification")
console.log("----------------------------------------------")
// Cette vérification sera faite lors des tests API

// Étape 5: Vérifier les données en base
console.log("\n💾 ÉTAPE 5: Suggestions pour vérifier les données")
console.log("------------------------------------------------")
console.log("Exécutez ces commandes dans Tinker (php artisan tinker):")
console.log("User::find(1)->transactions()->count()")
console.log("User::find(1)->comptes()->count()")
console.log("User::find(1)->objectifs()->count()")

// Lancer les tests
console.log("\n🚀 LANCEMENT DES TESTS...")
console.log("=========================")
runAllTests()

// Fonction utilitaire pour forcer le rechargement
window.forceReloadCharts = () => {
  console.log("🔄 Rechargement forcé des graphiques...")
  if (typeof loadChartsData === "function") {
    loadChartsData()
  } else {
    console.log("❌ Fonction loadChartsData non trouvée")
  }
}

console.log("\n💡 COMMANDES UTILES:")
console.log("====================")
console.log("forceReloadCharts() - Recharger les graphiques")
console.log("testAPIs() - Tester les APIs")
console.log("Chart.instances - Voir les instances de graphiques")
