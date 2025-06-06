-- Vérifier les types de comptes en base de données

-- Voir tous les comptes avec leurs types
SELECT 
    id,
    nom_compte,
    type_compte,
    solde,
    user_id,
    created_at
FROM compte 
ORDER BY created_at DESC 
LIMIT 10;

-- Compter les comptes par type
SELECT 
    type_compte,
    COUNT(*) as nombre
FROM compte 
GROUP BY type_compte;

-- Vérifier le dernier compte créé
SELECT 
    id,
    nom_compte,
    type_compte,
    solde,
    created_at
FROM compte 
ORDER BY created_at DESC 
LIMIT 1;

-- Vérifier s'il y a des valeurs nulles ou vides
SELECT 
    COUNT(*) as total,
    COUNT(CASE WHEN type_compte IS NULL THEN 1 END) as null_types,
    COUNT(CASE WHEN type_compte = '' THEN 1 END) as empty_types
FROM compte;
