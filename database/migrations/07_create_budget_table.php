<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('budget', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant_limite', 10, 2);
            $table->enum('periode', ['mensuel', 'hebdomadaire', 'annuel']);
            $table->foreignId('user_id')->constrained('utilisateur')->onDelete('cascade');
            $table->foreignId('categorie_id')->constrained('categorie')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('budget');
    }
};