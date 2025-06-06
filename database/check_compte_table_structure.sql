-- Vérifier la structure actuelle de la table compte/comptes
SHOW TABLES LIKE '%compte%';

-- Vérifier la structure de la table compte
DESCRIBE compte;

-- Vérifier s'il y a des données existantes
SELECT COUNT(*) as total_comptes FROM compte;

-- Voir quelques exemples de données
SELECT * FROM compte LIMIT 3;

-- Vérifier les contraintes de clés étrangères
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'compte'
AND REFERENCED_TABLE_NAME IS NOT NULL;
