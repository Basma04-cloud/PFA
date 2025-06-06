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
        Schema::table('notification', function (Blueprint $table) {
            // Ajouter les nouvelles colonnes si elles n'existent pas
            if (!Schema::hasColumn('notification', 'titre')) {
                $table->string('titre')->nullable()->after('id');
            }
            
            if (!Schema::hasColumn('notification', 'type')) {
                $table->string('type')->default('info')->after('titre');
            }
            
            if (!Schema::hasColumn('notification', 'data')) {
                $table->json('data')->nullable()->after('type');
            }
            
            if (!Schema::hasColumn('notification', 'lu_at')) {
                $table->timestamp('lu_at')->nullable()->after('lu');
            }
        });

        // Mettre à jour les notifications existantes avec des titres par défaut
        DB::table('notification')
            ->whereNull('titre')
            ->update(['titre' => 'Notification']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification', function (Blueprint $table) {
            if (Schema::hasColumn('notification', 'titre')) {
                $table->dropColumn('titre');
            }
            if (Schema::hasColumn('notification', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('notification', 'data')) {
                $table->dropColumn('data');
            }
            if (Schema::hasColumn('notification', 'lu_at')) {
                $table->dropColumn('lu_at');
            }
        });
    }
};
