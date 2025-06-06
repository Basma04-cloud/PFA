<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Spécifier explicitement le nom de la table si nécessaire
    protected $table = 'transactions';

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

    // Relation avec le compte - spécifier la clé étrangère si nécessaire
    public function compte()
    {
        return $this->belongsTo(Compte::class, 'compte_id', 'id');
    }

    // Accesseur pour formater le montant
    public function getMontantFormatteAttribute()
    {
        return number_format($this->montant, 2, ',', ' ') . ' €';
    }

    // Accesseur pour déterminer si c'est une dépense
    public function getIsDepenseAttribute()
    {
        return $this->montant < 0;
    }

    // Accesseur pour déterminer si c'est un revenu
    public function getIsRevenuAttribute()
    {
        return $this->montant > 0;
    }

    // Scope pour les dépenses (montants négatifs)
    public function scopeDepenses($query)
    {
        return $query->where('montant', '<', 0);
    }

    // Scope pour les revenus (montants positifs)
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
