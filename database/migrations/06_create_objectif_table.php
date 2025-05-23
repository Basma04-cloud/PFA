<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('objectif', function (Blueprint $table) {
            $table->id();
            $table->string('nom_objectif');
            $table->decimal('montant_vise', 10, 2);
            $table->decimal('montant_atteint', 10, 2)->default(0);
            $table->date('echeance')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained('utilisateur')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('objectif');
    }
};
