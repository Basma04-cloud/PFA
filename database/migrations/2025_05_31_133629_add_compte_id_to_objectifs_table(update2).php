<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
public function up(): void
{
    Schema::table('objectifs', function (Blueprint $table) {
        if (!Schema::hasColumn('objectifs', 'compte_id')) {
            $table->foreignId('compte_id')->constrained('compte')->onDelete('cascade'); }
});
}



    

    public function down(): void
    {
        Schema::table('objectifs', function (Blueprint $table) {
            $table->dropForeign(['compte_id']);
            $table->dropColumn('compte_id');
        });
    }
};

