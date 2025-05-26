<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UtilisateurSeeder extends Seeder
{
    public function run()
    {
        // Vérifier si la table 'utilisateur' existe
        if (Schema::hasTable('utilisateur')) {
            DB::table('utilisateur')->insert([
                [
                    'id' => 1,
                    'name' => 'Admin',
                    'email' => 'admin@example.com',
                    'password' => Hash::make('password'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 2,
                    'name' => 'Utilisateur Test',
                    'email' => 'test@example.com',
                    'password' => Hash::make('password'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 3,
                    'name' => 'Utilisateur Demo',
                    'email' => 'demo@example.com',
                    'password' => Hash::make('password'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
