-- Vérifier la structure actuelle des tables
SHOW CREATE TABLE compte;
SHOW CREATE TABLE transactions;

-- Vérifier les données dans la table compte
SELECT id, nom_compte, user_id FROM compte;

-- Vérifier les contraintes de clé étrangère
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'pfa' 
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Si nécessaire, supprimer et recréer la contrainte
-- ALTER TABLE transactions DROP FOREIGN KEY transactions_compte_id_foreign;
-- ALTER TABLE transactions ADD CONSTRAINT transactions_compte_id_foreign 
--     FOREIGN KEY (compte_id) REFERENCES compte(id) ON DELETE CASCADE;
