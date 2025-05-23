<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Budget extends Model
{
    use HasFactory;

    protected $table = 'budget';

    protected $fillable = [
        'montant_limite',
        'periode',
        'user_id',
        'categorie_id',
    ];

    protected $casts = [
        'montant_limite' => 'decimal:2',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'user_id');
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function getFormattedMontantLimiteAttribute()
    {
        return number_format($this->montant_limite, 2, ',', ' ') . ' €';
    }

    public function getMontantUtiliseAttribute()
    {
        $dateDebut = $this->getDateDebutPeriode();
        $dateFin = $this->getDateFinPeriode();

        return Transaction::where('user_id', $this->user_id)
            ->where('categorie_id', $this->categorie_id)
            ->where('type', 'dépense')
            ->whereBetween('date', [$dateDebut, $dateFin])
            ->sum('montant');
    }

    public function getProgressPercentAttribute()
    {
        if ($this->montant_limite <= 0) {
            return 0;
        }
        
        return min(100, round(($this->montant_utilise / $this->montant_limite) * 100));
    }

    public function getDateDebutPeriode()
    {
        $now = Carbon::now();
        
        switch ($this->periode) {
            case 'hebdomadaire':
                return $now->startOfWeek();
            case 'mensuel':
                return $now->copy()->startOfMonth();
            case 'annuel':
                return $now->copy()->startOfYear();
            default:
                return $now->copy()->startOfMonth();
        }
    }

    public function getDateFinPeriode()
    {
        $now = Carbon::now();
        
        switch ($this->periode) {
            case 'hebdomadaire':
                return $now->endOfWeek();
            case 'mensuel':
                return $now->copy()->endOfMonth();
            case 'annuel':
                return $now->copy()->endOfYear();
            default:
                return $now->copy()->endOfMonth();
        }
    }
}