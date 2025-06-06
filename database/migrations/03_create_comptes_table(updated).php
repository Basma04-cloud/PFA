<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compte', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('type', ['courant', 'epargne', 'credit', 'investissement']);
            $table->decimal('solde', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compte');
    }
};
