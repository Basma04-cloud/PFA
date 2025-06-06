<?php

namespace App\Observers;

use App\Models\Objectif;
use App\Services\NotificationService;

class ObjectifObserver
{
    /**
     * Handle the Objectif "updated" event.
     */
    public function updated(Objectif $objectif): void
    {
        // Vérifier si l'objectif vient d'être atteint
        if ($objectif->isDirty('montant_atteint') || $objectif->isDirty('statut')) {
            $this->verifierStatutObjectif($objectif);
        }
    }

    /**
     * Handle the Objectif "saving" event.
     */
    public function saving(Objectif $objectif): void
    {
        // Calculer automatiquement le nouveau statut
        $ancienStatut = $objectif->getOriginal('statut');
        
        if ($objectif->getMontantAtteint() >= $objectif->getMontantVise() && $objectif->statut === 'actif') {
            $objectif->statut = 'atteint';
            
            // Si le statut change, on notifiera dans l'event "updated"
        }
    }

    private function verifierStatutObjectif(Objectif $objectif)
    {
        $ancienStatut = $objectif->getOriginal('statut');
        $nouveauStatut = $objectif->statut;
        
        // Notification si objectif atteint
        if ($ancienStatut === 'actif' && $nouveauStatut === 'atteint') {
            NotificationService::notifierObjectifAtteint($objectif);
        }
        
        // Notification si objectif proche (80%+)
        if ($objectif->pourcentage >= 80 && $objectif->pourcentage < 100 && $objectif->statut === 'actif') {
            NotificationService::notifierObjectifProche($objectif);
        }
    }
}
