<?php

namespace Database\Seeders;

use App\Models\Compte;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompteSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Récupérer le premier utilisateur ou en créer un
        $user = User::first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'Utilisateur Test',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Créer des comptes par défaut
        $comptes = [
            [
                'nom' => 'Compte principal',
                'type' => 'bancaire',
                'solde' => 1245.50,
                'description' => 'Compte bancaire principal pour les dépenses quotidiennes.',
                'user_id' => $user->id,
            ],
            [
                'nom' => 'Compte épargne',
                'type' => 'epargne',
                'solde' => 5000.00,
                'description' => 'Épargne pour les projets futurs.',
                'user_id' => $user->id,
            ],
            [
                'nom' => 'Portefeuille espèces',
                'type' => 'especes',
                'solde' => 150.00,
                'description' => 'Argent liquide disponible.',
                'user_id' => $user->id,
            ],
            [
                'nom' => 'Carte de crédit',
                'type' => 'credit',
                'solde' => -350.00,
                'description' => 'Carte de crédit pour les achats en ligne.',
                'user_id' => $user->id,
            ],
        ];

        foreach ($comptes as $compte) {
            Compte::create($compte);
        }
    }
}
