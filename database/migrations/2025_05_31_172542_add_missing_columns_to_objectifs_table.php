<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('objectifs', function (Blueprint $table) {
            // Ajoutez les colonnes manquantes
            if (!Schema::hasColumn('objectifs', 'montant_vise')) {
                $table->decimal('montant_vise', 15, 2);
            }
            if (!Schema::hasColumn('objectifs', 'montant_atteint')) {
                $table->decimal('montant_atteint', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('objectifs', 'date_echeance')) {
                $table->date('date_echeance');
            }
            if (!Schema::hasColumn('objectifs', 'statut')) {
                $table->enum('statut', ['actif', 'atteint', 'abandonné'])->default('actif');
            }
            if (!Schema::hasColumn('objectifs', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('objectifs', function (Blueprint $table) {
            $table->dropColumn(['montant_vise', 'montant_atteint', 'date_echeance', 'statut', 'user_id']);
        });
    }
};
