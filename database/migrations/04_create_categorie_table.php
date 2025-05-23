<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('categorie', function (Blueprint $table) {
            $table->id();
            $table->string('nom_categorie');
            $table->enum('type', ['revenu', 'dépense']);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('categorie');
    }
};
