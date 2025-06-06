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
        // Vérifier quelle table existe réellement
        $compteTableExists = Schema::hasTable('compte');
        $comptesTableExists = Schema::hasTable('comptes');
        
        if ($compteTableExists && $comptesTableExists) {
            // Les deux tables existent - problème de migration
            throw new Exception('Les deux tables "compte" et "comptes" existent. Veuillez nettoyer votre base de données.');
        }
        
        if (!$compteTableExists && !$comptesTableExists) {
            // Aucune table n'existe - créer la table compte
            Schema::create('compte', function (Blueprint $table) {
                $table->id();
                $table->string('nom_compte');
                $table->string('type_compte');
                $table->decimal('solde', 15, 2)->default(0);
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
        
        if ($comptesTableExists && !$compteTableExists) {
            // Renommer la table comptes en compte pour correspondre à la contrainte
            Schema::rename('comptes', 'compte');
        }
        
        // Vérifier et corriger la contrainte de clé étrangère
        if (Schema::hasTable('transactions')) {
            // Supprimer l'ancienne contrainte si elle existe
            try {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->dropForeign(['compte_id']);
                });
            } catch (Exception $e) {
                // La contrainte n'existe peut-être pas
            }
            
            // Recréer la contrainte correcte
            Schema::table('transactions', function (Blueprint $table) {
                $table->foreign('compte_id')->references('id')->on('compte')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionnel: logique pour annuler les changements
    }
};
