<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objectif extends Model
{
    use HasFactory;

    protected $table = 'objectif';

    protected $fillable = [
        'nom_objectif',
        'montant_vise',
        'montant_atteint',
        'echeance',
        'description',
        'user_id',
    ];

    protected $casts = [
        'montant_vise' => 'decimal:2',
        'montant_atteint' => 'decimal:2',
        'echeance' => 'date',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'user_id');
    }

    public function getProgressPercentAttribute()
    {
        if ($this->montant_vise <= 0) {
            return 0;
        }
        
        return min(100, round(($this->montant_atteint / $this->montant_vise) * 100));
    }

    public function getFormattedMontantViseAttribute()
    {
        return number_format($this->montant_vise, 2, ',', ' ') . ' €';
    }

    public function getFormattedMontantAtteintAttribute()
    {
        return number_format($this->montant_atteint, 2, ',', ' ') . ' €';
    }

    public function getFormattedEcheanceAttribute()
    {
        return $this->echeance ? $this->echeance->format('d/m/Y') : 'Non définie';
    }

    public function getRemainingDaysAttribute()
    {
        if (!$this->echeance) {
            return null;
        }
        
        return max(0, now()->diffInDays($this->echeance, false));
    }
}
