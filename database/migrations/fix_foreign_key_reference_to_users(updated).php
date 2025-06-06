<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la table compte existe
        if (!Schema::hasTable('compte')) {
            throw new Exception('La table "compte" n\'existe pas');
        }

        // Vérifier si la table users existe
        if (!Schema::hasTable('users')) {
            throw new Exception('La table "users" n\'existe pas');
        }

        // Vérifier la contrainte actuelle
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME, REFERENCED_TABLE_NAME
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'compte'
            AND COLUMN_NAME = 'user_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        echo "Contraintes actuelles:\n";
        foreach ($constraints as $constraint) {
            echo "- {$constraint->CONSTRAINT_NAME} -> {$constraint->REFERENCED_TABLE_NAME}\n";
        }

        Schema::table('compte', function (Blueprint $table) use ($constraints) {
            // Supprimer toutes les contraintes existantes sur user_id
            foreach ($constraints as $constraint) {
                try {
                    echo "Suppression de la contrainte: {$constraint->CONSTRAINT_NAME}\n";
                    $table->dropForeign($constraint->CONSTRAINT_NAME);
                } catch (Exception $e) {
                    echo "Erreur lors de la suppression de {$constraint->CONSTRAINT_NAME}: {$e->getMessage()}\n";
                }
            }

            // Recréer la contrainte correcte vers la table 'users'
            echo "Création de la nouvelle contrainte vers 'users'\n";
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        echo "✅ Contrainte corrigée avec succès !\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compte', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
