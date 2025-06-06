<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Services\NotificationService;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        // Notification immédiate pour nouvelle transaction
        NotificationService::notifierNouvelleTransaction($transaction);
        
        // Vérifier les budgets
        NotificationService::verifierBudgets($transaction->user_id, $transaction);
        
        // Vérifier les objectifs
        NotificationService::verifierObjectifsAtteints($transaction->user_id);
        
        // Vérifier le solde du compte
        NotificationService::verifierSoldeCompte($transaction->compte);
        
        // Vérifier si c'est une grosse transaction
        NotificationService::verifierGrosseTransaction($transaction);
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        // Re-vérifier les budgets et objectifs lors de modification
        NotificationService::verifierBudgets($transaction->user_id, $transaction);
        NotificationService::verifierObjectifsAtteints($transaction->user_id);
    }
}
