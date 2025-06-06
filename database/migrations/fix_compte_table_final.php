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
        // Vérifier quelle table existe
        $compteExists = Schema::hasTable('compte');
        $comptesExists = Schema::hasTable('comptes');
        
        echo "Table 'compte' existe: " . ($compteExists ? 'OUI' : 'NON') . "\n";
        echo "Table 'comptes' existe: " . ($comptesExists ? 'OUI' : 'NON') . "\n";
        
        // Si aucune table n'existe, créer la table compte
        if (!$compteExists && !$comptesExists) {
            echo "Création de la table 'compte'\n";
            Schema::create('compte', function (Blueprint $table) {
                $table->id();
                $table->string('nom_compte');
                $table->string('type_compte');
                $table->decimal('solde', 15, 2)->default(0);
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->text('description')->nullable();
                $table->timestamps();
                
                // Index pour optimiser les requêtes
                $table->index(['user_id']);
                $table->index(['user_id', 'type_compte']);
            });
        }
        
        // Si la table comptes existe mais pas compte, renommer
        if ($comptesExists && !$compteExists) {
            echo "Renommage de 'comptes' vers 'compte'\n";
            Schema::rename('comptes', 'compte');
            $compteExists = true;
        }
        
        // Vérifier et ajouter les colonnes manquantes
        if ($compteExists) {
            Schema::table('compte', function (Blueprint $table) {
                // Vérifier et ajouter les colonnes une par une
                if (!Schema::hasColumn('compte', 'nom_compte')) {
                    echo "Ajout de la colonne 'nom_compte'\n";
                    $table->string('nom_compte')->after('id');
                }
                
                if (!Schema::hasColumn('compte', 'type_compte')) {
                    echo "Ajout de la colonne 'type_compte'\n";
                    $table->string('type_compte')->after('nom_compte');
                }
                
                if (!Schema::hasColumn('compte', 'solde')) {
                    echo "Ajout de la colonne 'solde'\n";
                    $table->decimal('solde', 15, 2)->default(0)->after('type_compte');
                }
                
                if (!Schema::hasColumn('compte', 'user_id')) {
                    echo "Ajout de la colonne 'user_id'\n";
                    $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->after('solde');
                }
                
                if (!Schema::hasColumn('compte', 'description')) {
                    echo "Ajout de la colonne 'description'\n";
                    $table->text('description')->nullable()->after('user_id');
                }
            });
        }
        
        // Vérifier la structure finale
        $columns = DB::select("DESCRIBE compte");
        echo "Structure finale de la table 'compte':\n";
        foreach ($columns as $column) {
            echo "- {$column->Field} ({$column->Type})\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionnel: logique pour annuler les changements
        Schema::dropIfExists('compte');
    }
};
