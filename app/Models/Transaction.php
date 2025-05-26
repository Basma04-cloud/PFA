<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'montant',
        'date',
        'categorie',
        'compte_id',
        'type',
        'description',
        'user_id'
    ];

    protected $casts = [
        'date' => 'date',
        'montant' => 'decimal:2'
    ];

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec le compte
    public function compte()
    {
        return $this->belongsTo(Compte::class);
    }

    // Accesseur pour la catégorie (si vous voulez une relation plus tard)
    public function getCategorieAttribute($value)
    {
        return $value;
    }

    // Scope pour les dépenses
    public function scopeDepenses($query)
    {
        return $query->where('montant', '<', 0);
    }

    // Scope pour les revenus
    public function scopeRevenus($query)
    {
        return $query->where('montant', '>', 0);
    }

    // Scope pour le mois courant
    public function scopeMoisCourant($query)
    {
        return $query->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year);
    }
}

