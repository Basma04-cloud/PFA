<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la table compte existe
        if (Schema::hasTable('compte')) {
            Schema::table('compte', function (Blueprint $table) {
                // Ajouter la colonne description si elle n'existe pas
                if (!Schema::hasColumn('compte', 'description')) {
                    $table->text('description')->nullable()->after('user_id');
                }
                
                // Ajouter d'autres colonnes manquantes si nécessaire
                if (!Schema::hasColumn('compte', 'nom_compte')) {
                    $table->string('nom_compte')->after('id');
                }
                
                if (!Schema::hasColumn('compte', 'type_compte')) {
                    $table->string('type_compte')->after('nom_compte');
                }
                
                if (!Schema::hasColumn('compte', 'solde')) {
                    $table->decimal('solde', 15, 2)->default(0)->after('type_compte');
                }
                
                if (!Schema::hasColumn('compte', 'user_id')) {
                    $table->foreignId('user_id')->constrained()->onDelete('cascade')->after('solde');
                }
            });
        } else {
            // Si la table n'existe pas, la créer complètement
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('compte')) {
            Schema::table('compte', function (Blueprint $table) {
                if (Schema::hasColumn('compte', 'description')) {
                    $table->dropColumn('description');
                }
            });
        }
    }
};
