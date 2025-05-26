<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        // Données de test pour les notifications
        $notifications = [
            (object) [
                'id' => 1,
                'titre' => 'Nouvelle transaction enregistrée',
                'message' => 'Une transaction de -85,50 € a été ajoutée',
                'lu' => false,
                'created_at' => now()->subHours(2)
            ],
            (object) [
                'id' => 2,
                'titre' => 'Objectif atteint !',
                'message' => 'Félicitations ! Vous avez atteint votre objectif',
                'lu' => false,
                'created_at' => now()->subDays(1)
            ],
            (object) [
                'id' => 3,
                'titre' => 'Nouvelle transaction enregistrée',
                'message' => 'Une transaction de +2000,00 € a été ajoutée',
                'lu' => true,
                'created_at' => now()->subDays(2)
            ],
            (object) [
                'id' => 4,
                'titre' => 'Budget dépassé',
                'message' => 'Votre budget alimentation a été dépassé',
                'lu' => true,
                'created_at' => now()->subDays(3)
            ]
        ];

        return view('notifications.index', compact('notifications'));
    }

    public function marquerCommeLu($id)
    {
        // Logique pour marquer une notification comme lue
        return redirect()->route('notifications.index')->with('success', 'Notification marquée comme lue');
    }

    public function marquerToutCommeLu()
    {
        // Logique pour marquer toutes les notifications comme lues
        return redirect()->route('notifications.index')->with('success', 'Toutes les notifications ont été marquées comme lues');
    }

    public function destroy($id)
    {
        // Logique pour supprimer une notification
        return redirect()->route('notifications.index')->with('success', 'Notification supprimée');
    }
}
