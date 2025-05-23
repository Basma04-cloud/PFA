<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('invitation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediteur_id')->constrained('utilisateur')->onDelete('cascade');
            $table->string('destinataire_email');
            $table->enum('statut', ['en attente', 'acceptée', 'refusée'])->default('en attente');
            $table->timestamp('date_envoi')->useCurrent();
        });
    }

    public function down() {
        Schema::dropIfExists('invitation');
    }
};
