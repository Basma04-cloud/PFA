<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Objectif extends Model
{
    use HasFactory;

    protected $table = 'objectifs';

    protected $fillable = [
        'nom',
        'description',
        'montant_vise',
        'montant_atteint',
        'date_echeance',
        'statut',
        'user_id',
        'compte_id'
    ];

    protected $casts = [
        'montant_vise' => 'decimal:2',
        'montant_atteint' => 'decimal:2',
        'date_echeance' => 'date'
    ];

    protected $attributes = [
        'montant_atteint' => 0,
        'statut' => 'actif'
    ];

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function compte()
{
    return $this->belongsTo(Compte::class);
}

    
    // Calculer le pourcentage de progression (corrigé)
    public function getPourcentageAttribute()
    {
        $montantVise = $this->getMontantVise();
        $montantAtteint = $this->getMontantAtteint();
        
        if ($montantVise <= 0) {
            return 0;
        }
        
        // Calculer le pourcentage réel
        $pourcentage = ($montantAtteint / $montantVise) * 100;
        
        // Pour l'affichage de la barre de progression, limiter à 100%
        return round($pourcentage, 2);
    }

    // Pourcentage pour la barre de progression (limité à 100%)
    public function getPourcentageBarreAttribute()
    {
        return min($this->pourcentage, 100);
    }

    // Vérifier si l'objectif est atteint
    public function getIsAtteintAttribute()
    {
        return $this->getMontantAtteint() >= $this->getMontantVise();
    }

    // Vérifier si l'objectif est dépassé
    public function getIsDepasseAttribute()
    {
        return $this->getMontantAtteint() > $this->getMontantVise();
    }

    // Méthodes pour obtenir les montants
    public function getMontantVise()
    {
        return $this->montant_vise ?? 0;
    }

    public function getMontantAtteint()
    {
        return $this->montant_atteint ?? 0;
    }

    // Calculer le montant restant (peut être négatif si dépassé)
    public function getMontantRestantAttribute()
    {
        return $this->getMontantVise() - $this->getMontantAtteint();
    }

    // Calculer le montant en excès si dépassé
    public function getMontantExcesAttribute()
    {
        $excès = $this->getMontantAtteint() - $this->getMontantVise();
        return max($excès, 0);
    }

    // Accesseurs pour les montants formatés
    public function getMontantViseFormatteAttribute()
    {
        return number_format($this->getMontantVise(), 2, ',', ' ') . ' €';
    }

    public function getMontantAtteintFormatteAttribute()
    {
        return number_format($this->getMontantAtteint(), 2, ',', ' ') . ' €';
    }

    public function getMontantRestantFormatteAttribute()
    {
        $restant = $this->montant_restant;
        $signe = $restant < 0 ? '+' : '';
        return $signe . number_format(abs($restant), 2, ',', ' ') . ' €';
    }

    // Mettre à jour automatiquement le statut
    public function mettreAJourStatut()
{
    $ancienStatut = $this->statut;

    if ($this->montant_atteint >= $this->montant_vise) {
        $this->statut = 'atteint';
    } elseif ($this->statut !== 'abandonne') {
        $this->statut = 'actif';
    }

    $this->save();

    // Si le statut vient de changer à "atteint", envoyer une notification
    if ($ancienStatut !== 'atteint' && $this->statut === 'atteint') {
        \App\Models\Notification::creerNotification(
            $this->user_id,
            "Félicitations ! Vous avez atteint votre objectif : <strong>{$this->nom}</strong> 🎉",
            'Objectif atteint',
            'success'
        );
    }
}


    // Ajouter une contribution
    public function ajouterContribution($montant)
    {
        if ($montant <= 0) {
            throw new \InvalidArgumentException('Le montant doit être positif');
        }
        
        $this->montant_atteint += $montant;
        $this->save();
        
        // Mettre à jour le statut automatiquement
        $this->mettreAJourStatut();
        
        return $this;
    }

    // Accesseur pour le statut formaté
    public function getStatutFormatteAttribute()
    {
        $statuts = [
            'actif' => $this->is_atteint ? 'Atteint' : 'En cours',
            'atteint' => 'Atteint',
            'abandonne' => 'Abandonné'
        ];

        return $statuts[$this->statut] ?? ucfirst($this->statut);
    }

    // Accesseur pour la couleur du statut
    public function getStatutCouleurAttribute()
    {
        if ($this->is_atteint) {
            return 'green';
        }
        
        $couleurs = [
            'actif' => 'blue',
            'atteint' => 'green',
            'abandonne' => 'red'
        ];

        return $couleurs[$this->statut] ?? 'gray';
    }

    // Scopes
    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeAtteints($query)
    {
        return $query->where('statut', 'atteint');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Event listeners pour mettre à jour automatiquement le statut
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($objectif) {
            // Mettre à jour le statut avant la sauvegarde
            if ($objectif->getMontantAtteint() >= $objectif->getMontantVise() && $objectif->statut === 'actif') {
                $objectif->statut = 'atteint';
            }
        });
    }
}

