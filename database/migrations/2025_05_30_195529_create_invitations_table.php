<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { Schema::create('invitations', function (Blueprint $table) {
    $table->id();
    $table->string('email')->unique();
    $table->string('token')->unique();
    $table->foreignId('invited_by')->constrained('users')->onDelete('cascade');
    $table->timestamp('expires_at')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invitations');
    }
};
