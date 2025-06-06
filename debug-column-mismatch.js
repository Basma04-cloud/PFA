console.log("🔍 DIAGNOSTIC DU PROBLÈME DE COLONNE")
console.log("===================================\n")

console.log("❌ ERREUR DÉTECTÉE:")
console.log("SQLSTATE[42S22]: Column not found: 1054 Unknown column 'montant_actuel'")
console.log("Le code essaie d'utiliser 'montant_actuel' mais la colonne n'existe pas\n")

console.log("🔍 VÉRIFICATION NÉCESSAIRE:")
console.log("1. Vérifier la structure réelle de la table objectifs")
console.log("2. Corriger le modèle selon la structure existante")
console.log("3. Ou créer une migration pour ajouter la colonne manquante\n")
