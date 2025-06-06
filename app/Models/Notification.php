<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Notification extends Model
{
    use HasFactory;

    // Utiliser votre table existante
    protected $table = 'notification';

    protected $fillable = [
        'message',
        'lu',
        'date_envoi',
        'user_id',
        // Nouveaux champs (optionnels)
        'titre',
        'type',
        'data',
        'lu_at'
    ];

    protected $casts = [
        'lu' => 'boolean',
        'date_envoi' => 'datetime',
        'data' => 'array',
        'lu_at' => 'datetime'
    ];

    // Constructeur pour adapter aux colonnes existantes
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        // Ajuster les fillable selon les colonnes qui existent
        $this->fillable = ['message', 'lu', 'date_envoi', 'user_id'];
        
        if (Schema::hasColumn('notifications', 'titre')) {
            $this->fillable[] = 'titre';
        }
        if (Schema::hasColumn('notifications', 'type')) {
            $this->fillable[] = 'type';
        }
        if (Schema::hasColumn('notifications', 'data')) {
            $this->fillable[] = 'data';
            $this->casts['data'] = 'array';
        }
        if (Schema::hasColumn('notifications', 'lu_at')) {
            $this->fillable[] = 'lu_at';
            $this->casts['lu_at'] = 'datetime';
        }
    }

    // Relation avec l'utilisateur (adapter selon votre table)
    public function user()
    {
        // Vérifier si vous utilisez 'users' ou 'utilisateur'
        $userTable = Schema::hasTable('utilisateur') ? 'utilisateur' : 'users';
        return $this->belongsTo(User::class, 'user_id');
    }

    // Méthodes compatibles avec l'ancienne et nouvelle structure
    public function marquerCommeLu()
    {
        $updateData = ['lu' => true];
        
        if (Schema::hasColumn('notifications', 'lu_at')) {
            $updateData['lu_at'] = now();
        }
        
        $this->update($updateData);
    }

    // Accesseurs adaptatifs
    public function getTitreAttribute($value)
    {
        // Si pas de titre, générer un titre basé sur le message
        if (!$value && $this->message) {
            return substr($this->message, 0, 50) . (strlen($this->message) > 50 ? '...' : '');
        }
        return $value ?: 'Notification';
    }

    public function getTypeAttribute($value)
    {
        return $value ?: 'info';
    }

    public function getTimeAgoAttribute()
    {
        $dateField = Schema::hasColumn('notification', 'date_envoi') ? 'date_envoi' : 'created_at';
        return $this->{$dateField}->diffForHumans();
    }

    public function getIconeAttribute()
    {
        $type = $this->type;
        $icones = [
            'info' => '📢',
            'success' => '✅',
            'warning' => '⚠️',
            'error' => '❌',
            'transaction' => '💰',
            'objectif' => '🎯',
            'compte' => '🏦',
            'budget' => '📊'
        ];

        return $icones[$type] ?? '📢';
    }

    public function getCouleurAttribute()
    {
        $type = $this->type;
        $couleurs = [
            'info' => 'blue',
            'success' => 'green',
            'warning' => 'yellow',
            'error' => 'red',
            'transaction' => 'purple',
            'objectif' => 'indigo',
            'compte' => 'teal',
            'budget' => 'orange'
        ];

        return $couleurs[$type] ?? 'blue';
    }

    // Scopes
    public function scopeNonLues($query)
    {
        return $query->where('lu', false);
    }

    public function scopeLues($query)
    {
        return $query->where('lu', true);
    }

    public function scopeParType($query, $type)
    {
        if (Schema::hasColumn('notification', 'type')) {
            return $query->where('type', $type);
        }
        return $query;
    }

    public function scopeRecentes($query, $jours = 30)
    {
        $dateField = Schema::hasColumn('notification', 'date_envoi') ? 'date_envoi' : 'created_at';
        return $query->where($dateField, '>=', now()->subDays($jours));
    }
    

    // Méthodes statiques pour créer des notifications (compatibles)
    public static function creerNotification($userId, $message, $titre = null, $type = 'info', $data = null)
    {
        $notificationData = [
            'user_id' => $userId,
            'message' => $message,
            'date_envoi' => now(),
            'type' => $type,
            'lu' => false

        ];

        // Ajouter les champs optionnels s'ils existent
        if (Schema::hasColumn('notification', 'titre') && $titre) {
            $notificationData['titre'] = $titre;
        }
        if (Schema::hasColumn('notification', 'type')) {
            $notificationData['type'] = $type;
        }
        if (Schema::hasColumn('notification', 'data') && $data) {
            $notificationData['data'] = $data;
        }

        return self::create($notificationData);
    }

    public static function creerNotificationTransaction($userId, $transaction)
    {
        $montant = number_format(abs($transaction->montant), 2, ',', ' ');
        $signe = $transaction->montant > 0 ? '+' : '-';
        $message = "Transaction {$signe}{$montant} € ajoutée sur {$transaction->compte->nom_compte}";
        
        return self::creerNotification(
            $userId,
            $message,
            'Nouvelle transaction',
            'transaction',
            [
                'transaction_id' => $transaction->id,
                'montant' => $transaction->montant,
                'compte' => $transaction->compte->nom_compte
            ]
        );
    }

    public static function creerNotificationObjectif($userId, $objectif, $typeNotif = 'info')
    {
        $messages = [
            'atteint' => "Félicitations ! Objectif '{$objectif->nom}' atteint !",
            'proche' => "Vous êtes proche de votre objectif '{$objectif->nom}' !",
            'rappel' => "N'oubliez pas votre objectif '{$objectif->nom}'"
        ];

        return self::creerNotification(
            $userId,
            $messages[$typeNotif] ?? $messages['rappel'],
            'Objectif ' . ucfirst($typeNotif),
            'objectif',
            [
                'objectif_id' => $objectif->id,
                'progression' => $objectif->pourcentage ?? 0
            ]
        );
    }
}
