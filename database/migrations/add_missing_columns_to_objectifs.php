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
        Schema::table('objectifs', function (Blueprint $table) {
            // Ajouter montant_atteint si elle n'existe pas
            if (!Schema::hasColumn('objectifs', 'montant_atteint')) {
                $table->decimal('montant_atteint', 10, 2)->default(0)->after('montant_vise');
            }
            
            // Ajouter statut si elle n'existe pas
            if (!Schema::hasColumn('objectifs', 'statut')) {
                $table->enum('statut', ['actif', 'atteint', 'abandonne'])->default('actif')->after('date_echeance');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('objectifs', function (Blueprint $table) {
            if (Schema::hasColumn('objectifs', 'montant_atteint')) {
                $table->dropColumn('montant_atteint');
            }
            if (Schema::hasColumn('objectifs', 'statut')) {
                $table->dropColumn('statut');
            }
        });
    }
};
