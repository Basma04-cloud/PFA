-- Vérifier les tables existantes
SHOW TABLES LIKE '%user%';

-- Vérifier la structure de la contrainte actuelle
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'pfa' 
AND TABLE_NAME = 'compte'
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Vérifier si l'utilisateur existe dans la table users
SELECT id, name, email FROM users WHERE id = 3;

-- Vérifier si la table utilisateur existe
SELECT COUNT(*) as count FROM information_schema.tables 
WHERE table_schema = 'pfa' AND table_name = 'utilisateur';
