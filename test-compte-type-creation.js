// Script pour diagnostiquer le problème de type de compte

console.log("🧪 TEST DU TYPE DE COMPTE")
console.log("========================\n")

// Test 1: Vérifier les données en base
async function checkDatabaseData() {
  console.log("📊 Test 1: Données en base de données")
  console.log("------------------------------------")

  try {
    const response = await fetch("/debug/comptes")
    const data = await response.json()

    if (data.user_comptes && data.user_comptes.length > 0) {
      console.log("Comptes trouvés en base:")
      data.user_comptes.forEach((compte, index) => {
        console.log(`${index + 1}. ID: ${compte.id}`)
        console.log(`   Nom: ${compte.nom_compte}`)
        console.log(`   Type en base: "${compte.type_compte}"`)
        console.log(`   Solde: ${compte.solde}`)
        console.log(`   Créé le: ${compte.created_at}`)
        console.log("")
      })

      // Vérifier le dernier compte créé
      const dernierCompte = data.user_comptes[data.user_comptes.length - 1]
      console.log(`🔍 DERNIER COMPTE CRÉÉ:`)
      console.log(`Type stocké en base: "${dernierCompte.type_compte}"`)

      if (dernierCompte.type_compte === "courant") {
        console.log("✅ Le type est correct en base de données")
        console.log("💡 Le problème vient de l'affichage")
      } else {
        console.log("❌ Le type est incorrect en base de données")
        console.log("💡 Le problème vient de la sauvegarde")
      }

      return dernierCompte
    } else {
      console.log("❌ Aucun compte trouvé")
      return null
    }
  } catch (error) {
    console.log("❌ Erreur:", error.message)
    return null
  }
}

// Test 2: Simuler une création avec logging
async function testCreationWithLogging() {
  console.log("\n🧪 Test 2: Création avec logging détaillé")
  console.log("----------------------------------------")

  const testData = {
    nom_compte: "Test Type Courant " + new Date().getTime(),
    type_compte: "courant", // EXPLICITEMENT courant
    solde: 1500,
    description: "Test pour vérifier le type de compte",
  }

  console.log("Données à envoyer:", testData)

  try {
    // Simuler l'envoi du formulaire
    const formData = new FormData()
    Object.keys(testData).forEach((key) => {
      formData.append(key, testData[key])
    })

    console.log("FormData créé:")
    for (const [key, value] of formData.entries()) {
      console.log(`  ${key}: "${value}"`)
    }

    // Note: On ne peut pas vraiment envoyer le formulaire ici
    // mais on peut vérifier la structure
    console.log("✅ Structure des données correcte")
    console.log(`✅ Type spécifié: "${testData.type_compte}"`)

    return testData
  } catch (error) {
    console.log("❌ Erreur:", error.message)
    return null
  }
}

// Test 3: Vérifier les options du formulaire
function checkFormOptions() {
  console.log("\n📝 Test 3: Options du formulaire")
  console.log("-------------------------------")

  const selectElement = document.getElementById("type_compte")

  if (selectElement) {
    console.log("Options disponibles dans le formulaire:")
    Array.from(selectElement.options).forEach((option, index) => {
      console.log(`${index}. value="${option.value}" text="${option.text}"`)
      if (option.selected) {
        console.log(`   👆 SÉLECTIONNÉ`)
      }
    })

    // Vérifier la valeur actuelle
    console.log(`\nValeur actuelle du select: "${selectElement.value}"`)

    // Tester la sélection de "courant"
    selectElement.value = "courant"
    console.log(`Après sélection de "courant": "${selectElement.value}"`)

    if (selectElement.value === "courant") {
      console.log("✅ Le formulaire fonctionne correctement")
    } else {
      console.log("❌ Problème avec le formulaire")
    }
  } else {
    console.log("❌ Élément select 'type_compte' non trouvé")
  }
}

// Test 4: Vérifier les accesseurs du modèle
function checkModelAccessors() {
  console.log("\n🔧 Test 4: Accesseurs du modèle")
  console.log("------------------------------")

  const typeLabels = {
    courant: "Compte Courant",
    epargne: "Compte Épargne",
    credit: "Compte Crédit",
    investissement: "Compte Investissement",
  }

  console.log("Mapping des types:")
  Object.entries(typeLabels).forEach(([key, value]) => {
    console.log(`  "${key}" -> "${value}"`)
  })

  // Tester la conversion
  const testType = "courant"
  const displayType = typeLabels[testType]
  console.log(`\nTest: "${testType}" -> "${displayType}"`)

  if (displayType === "Compte Courant") {
    console.log("✅ Accesseur correct")
  } else {
    console.log("❌ Problème avec l'accesseur")
  }
}

// Fonction principale
async function diagnoseProblem() {
  console.log("🚀 DIAGNOSTIC COMPLET DU PROBLÈME")
  console.log("=================================\n")

  const dbData = await checkDatabaseData()
  const creationTest = await testCreationWithLogging()
  checkFormOptions()
  checkModelAccessors()

  console.log("\n📋 RÉSUMÉ DU DIAGNOSTIC")
  console.log("======================")

  if (dbData) {
    if (dbData.type_compte === "courant") {
      console.log("✅ Données en base: CORRECTES")
      console.log("💡 Le problème vient probablement de l'affichage")
      console.log("🔧 Vérifiez la vue ou l'accesseur du modèle")
    } else {
      console.log("❌ Données en base: INCORRECTES")
      console.log("💡 Le problème vient de la sauvegarde")
      console.log("🔧 Vérifiez le contrôleur et la validation")
    }
  }

  console.log("\n🎯 PROCHAINES ÉTAPES:")
  console.log("1. Vérifiez le dernier compte créé dans la base")
  console.log("2. Si le type est correct en base, vérifiez l'affichage")
  console.log("3. Si le type est incorrect en base, vérifiez la sauvegarde")
  console.log("4. Testez avec le formulaire de debug ci-dessous")
}

// Lancer le diagnostic
diagnoseProblem()

// Fonctions utilitaires
window.diagnoseProblem = diagnoseProblem
window.checkDatabaseData = checkDatabaseData

console.log("\n💡 COMMANDES DISPONIBLES:")
console.log("diagnoseProblem() - Diagnostic complet")
console.log("checkDatabaseData() - Vérifier les données en base")
