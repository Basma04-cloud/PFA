<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objectif extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'montant_cible',
        'montant_actuel',
        'date_echeance',
        'user_id'
    ];

    protected $casts = [
        'montant_cible' => 'decimal:2',
        'montant_actuel' => 'decimal:2',
        'date_echeance' => 'date'
    ];

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Calculer le pourcentage de progression
    public function getPourcentageAttribute()
    {
        if ($this->montant_cible <= 0) {
            return 0;
        }
        return ($this->montant_actuel / $this->montant_cible) * 100;
    }
}
