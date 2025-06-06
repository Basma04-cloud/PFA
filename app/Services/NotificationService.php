<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Transaction;
use App\Models\Objectif;
use App\Models\Compte;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificationService
{
    // Seuils configurables
    const SEUIL_GROSSE_TRANSACTION = 1000;
    const SEUIL_OBJECTIF_PROCHE = 80; // %
    const JOURS_AVANT_ECHEANCE = 7;

    /**
     * Notifier une nouvelle transaction
     */
    public static function notifierNouvelleTransaction(Transaction $transaction)
    {
        try {
            $montant = number_format(abs($transaction->montant), 2, ',', ' ');
            $signe = $transaction->montant > 0 ? '+' : '-';
            $type = $transaction->montant > 0 ? 'success' : 'info';
            
            return Notification::creerNotification(
                $transaction->user_id,
                "Transaction {$signe}{$montant} € ajoutée sur {$transaction->compte->nom_compte}",
                'Nouvelle transaction',
                'transaction',
                [
                    'transaction_id' => $transaction->id,
                    'montant' => $transaction->montant,
                    'compte' => $transaction->compte->nom_compte,
                    'categorie' => $transaction->categorie
                ]
            );
        } catch (\Exception $e) {
            Log::error('Erreur notification transaction', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Vérifier et notifier les budgets dépassés
     */
    public static function verifierBudgets($userId, Transaction $transaction)
    {
        try {
            // Vérifier seulement pour les dépenses
            if ($transaction->montant >= 0) {
                return;
            }

            $categorie = $transaction->categorie;
            $moisCourant = now()->format('Y-m');

            // Calculer les dépenses du mois pour cette catégorie
            $depensesMois = abs(Transaction::where('user_id', $userId)
                ->where('categorie', $categorie)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('montant', '<', 0)
                ->sum('montant'));

            // Budgets par défaut (vous pouvez les stocker en base)
            $budgetsDefaut = [
                'Alimentation' => 500,
                'Logement' => 1200,
                'Transport' => 300,
                'Divertissement' => 200,
                'Santé' => 150,
                'Autres' => 100
            ];

            $budgetLimite = $budgetsDefaut[$categorie] ?? 200;

            // Vérifier si le budget est dépassé
            if ($depensesMois > $budgetLimite) {
                $pourcentage = round(($depensesMois / $budgetLimite) * 100, 1);
                
                // Vérifier si on n'a pas déjà notifié ce mois
                $notificationExiste = Notification::where('user_id', $userId)
                    ->where('type', 'budget')
                    ->whereJsonContains('data->categorie', $categorie)
                    ->whereJsonContains('data->mois', $moisCourant)
                    ->exists();

                if (!$notificationExiste) {
                    return Notification::creerNotification(
                        $userId,
                        "Budget '{$categorie}' dépassé ! {$depensesMois}€ / {$budgetLimite}€ ({$pourcentage}%)",
                        'Budget dépassé',
                        'budget',
                        [
                            'categorie' => $categorie,
                            'montant' => $depensesMois,
                            'limite' => $budgetLimite,
                            'pourcentage' => $pourcentage,
                            'mois' => $moisCourant
                        ]
                    );
                }
            }
            // Alerte à 90% du budget
            elseif ($depensesMois > ($budgetLimite * 0.9)) {
                $pourcentage = round(($depensesMois / $budgetLimite) * 100, 1);
                
                $notificationExiste = Notification::where('user_id', $userId)
                    ->where('type', 'warning')
                    ->whereJsonContains('data->categorie', $categorie)
                    ->whereJsonContains('data->mois', $moisCourant)
                    ->where('message', 'like', '%90%')
                    ->exists();

                if (!$notificationExiste) {
                    return Notification::creerNotification(
                        $userId,
                        "Attention ! Budget '{$categorie}' à {$pourcentage}% ({$depensesMois}€ / {$budgetLimite}€)",
                        'Budget bientôt dépassé',
                        'warning',
                        [
                            'categorie' => $categorie,
                            'montant' => $depensesMois,
                            'limite' => $budgetLimite,
                            'pourcentage' => $pourcentage,
                            'mois' => $moisCourant
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur vérification budget', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Vérifier et notifier les objectifs atteints
     */
    public static function verifierObjectifsAtteints($userId)
    {
        try {
            $objectifs = Objectif::where('user_id', $userId)
                                ->where('statut', 'actif')
                                ->get();

            foreach ($objectifs as $objectif) {
                $ancienPourcentage = 0;
                
                // Calculer l'ancien pourcentage si on a les données
                if ($objectif->getOriginal('montant_atteint')) {
                    $ancienPourcentage = ($objectif->getOriginal('montant_atteint') / $objectif->montant_vise) * 100;
                }

                $nouveauPourcentage = $objectif->pourcentage;

                // Objectif atteint
                if ($nouveauPourcentage >= 100 && $ancienPourcentage < 100) {
                    self::notifierObjectifAtteint($objectif);
                }
                // Objectif proche (80%+) et pas encore notifié
                elseif ($nouveauPourcentage >= self::SEUIL_OBJECTIF_PROCHE && $ancienPourcentage < self::SEUIL_OBJECTIF_PROCHE) {
                    self::notifierObjectifProche($objectif);
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur vérification objectifs', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Notifier objectif atteint
     */
    public static function notifierObjectifAtteint(Objectif $objectif)
    {
        try {
            $excedent = $objectif->montant_atteint - $objectif->montant_vise;
            $message = "🎉 Félicitations ! Objectif '{$objectif->nom}' atteint !";
            
            if ($excedent > 0) {
                $excedentFormatte = number_format($excedent, 2, ',', ' ');
                $message .= " Vous avez même dépassé de {$excedentFormatte}€ !";
            }

            return Notification::creerNotification(
                $objectif->user_id,
                $message,
                'Objectif atteint !',
                'success',
                [
                    'objectif_id' => $objectif->id,
                    'objectif_nom' => $objectif->nom,
                    'montant_vise' => $objectif->montant_vise,
                    'montant_atteint' => $objectif->montant_atteint,
                    'excedent' => $excedent
                ]
            );
        } catch (\Exception $e) {
            Log::error('Erreur notification objectif atteint', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Notifier objectif proche
     */
    public static function notifierObjectifProche(Objectif $objectif)
    {
        try {
            $pourcentage = round($objectif->pourcentage, 1);
            $restant = $objectif->montant_vise - $objectif->montant_atteint;
            $restantFormatte = number_format($restant, 2, ',', ' ');

            return Notification::creerNotification(
                $objectif->user_id,
                "🎯 Vous êtes proche de votre objectif '{$objectif->nom}' ! ({$pourcentage}% - reste {$restantFormatte}€)",
                'Objectif bientôt atteint',
                'info',
                [
                    'objectif_id' => $objectif->id,
                    'objectif_nom' => $objectif->nom,
                    'pourcentage' => $pourcentage,
                    'montant_restant' => $restant
                ]
            );
        } catch (\Exception $e) {
            Log::error('Erreur notification objectif proche', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Vérifier le solde du compte
     */
    public static function verifierSoldeCompte(Compte $compte)
    {
        try {
            // Notifier si le solde devient négatif
            if ($compte->solde < 0) {
                $soldeFormatte = number_format(abs($compte->solde), 2, ',', ' ');
                
                // Vérifier si on n'a pas déjà notifié aujourd'hui
                $notificationExiste = Notification::where('user_id', $compte->user_id)
                    ->where('type', 'error')
                    ->whereJsonContains('data->compte_id', $compte->id)
                    ->whereDate('created_at', today())
                    ->where('message', 'like', '%négatif%')
                    ->exists();

                if (!$notificationExiste) {
                    return Notification::creerNotification(
                        $compte->user_id,
                        "⚠️ Attention ! Le solde de votre compte '{$compte->nom_compte}' est négatif (-{$soldeFormatte}€)",
                        'Solde négatif',
                        'error',
                        [
                            'compte_id' => $compte->id,
                            'compte_nom' => $compte->nom_compte,
                            'solde' => $compte->solde
                        ]
                    );
                }
            }
            // Notifier si le solde est très bas (< 50€)
            elseif ($compte->solde < 50 && $compte->solde > 0) {
                $soldeFormatte = number_format($compte->solde, 2, ',', ' ');
                
                $notificationExiste = Notification::where('user_id', $compte->user_id)
                    ->where('type', 'warning')
                    ->whereJsonContains('data->compte_id', $compte->id)
                    ->whereDate('created_at', today())
                    ->where('message', 'like', '%solde bas%')
                    ->exists();

                if (!$notificationExiste) {
                    return Notification::creerNotification(
                        $compte->user_id,
                        "💰 Solde bas sur '{$compte->nom_compte}' : {$soldeFormatte}€",
                        'Solde bas',
                        'warning',
                        [
                            'compte_id' => $compte->id,
                            'compte_nom' => $compte->nom_compte,
                            'solde' => $compte->solde
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur vérification solde', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Vérifier les grosses transactions
     */
    public static function verifierGrosseTransaction(Transaction $transaction)
    {
        try {
            $montantAbs = abs($transaction->montant);
            
            if ($montantAbs >= self::SEUIL_GROSSE_TRANSACTION) {
                $montantFormatte = number_format($montantAbs, 2, ',', ' ');
                $type = $transaction->montant > 0 ? 'Gros revenu' : 'Grosse dépense';
                $icone = $transaction->montant > 0 ? '💰' : '💸';
                
                return Notification::creerNotification(
                    $transaction->user_id,
                    "{$icone} {$type} détecté : {$montantFormatte}€ sur {$transaction->compte->nom_compte}",
                    $type,
                    $transaction->montant > 0 ? 'success' : 'warning',
                    [
                        'transaction_id' => $transaction->id,
                        'montant' => $transaction->montant,
                        'seuil' => self::SEUIL_GROSSE_TRANSACTION
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Erreur vérification grosse transaction', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Vérifier les échéances proches (à appeler via une tâche planifiée)
     */
    public static function verifierEcheancesProches()
    {
        try {
            $dateLimit = now()->addDays(self::JOURS_AVANT_ECHEANCE);
            
            $objectifsProches = Objectif::where('statut', 'actif')
                ->where('date_echeance', '<=', $dateLimit)
                ->where('date_echeance', '>', now())
                ->get();

            foreach ($objectifsProches as $objectif) {
                $joursRestants = now()->diffInDays($objectif->date_echeance);
                
                // Vérifier si on n'a pas déjà notifié
                $notificationExiste = Notification::where('user_id', $objectif->user_id)
                    ->where('type', 'warning')
                    ->whereJsonContains('data->objectif_id', $objectif->id)
                    ->where('message', 'like', '%échéance%')
                    ->whereDate('created_at', today())
                    ->exists();

                if (!$notificationExiste) {
                    $message = $joursRestants == 1 
                        ? "⏰ Échéance demain pour l'objectif '{$objectif->nom}' !"
                        : "⏰ Plus que {$joursRestants} jours pour l'objectif '{$objectif->nom}' !";

                    Notification::creerNotification(
                        $objectif->user_id,
                        $message,
                        'Échéance proche',
                        'warning',
                        [
                            'objectif_id' => $objectif->id,
                            'objectif_nom' => $objectif->nom,
                            'jours_restants' => $joursRestants,
                            'pourcentage' => $objectif->pourcentage
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur vérification échéances', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Nettoyer les anciennes notifications
     */
    public static function nettoyerAnciennesNotifications($jours = 90)
    {
        try {
            return Notification::where('created_at', '<', now()->subDays($jours))
                              ->where('lu', true)
                              ->delete();
        } catch (\Exception $e) {
            Log::error('Erreur nettoyage notifications', ['error' => $e->getMessage()]);
            return 0;
        }
    }
}
