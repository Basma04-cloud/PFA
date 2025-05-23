<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('journal_admin', function (Blueprint $table) {
            $table->id();
            $table->text('action');
            $table->enum('type_action', ['ajout', 'modification', 'suppression']);
            $table->timestamp('date_action')->useCurrent();
            $table->foreignId('user_id')->constrained('utilisateur')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('journal_admin');
    }
};