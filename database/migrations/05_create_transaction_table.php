<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 10, 2);
            $table->enum('type', ['revenu', 'dépense']);
            $table->date('date');
            $table->text('description')->nullable();
            $table->foreignId('compte_id')->constrained('compte')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('utilisateur')->onDelete('cascade');
            $table->foreignId('categorie_id')->constrained('categorie')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('transaction');
    }
};