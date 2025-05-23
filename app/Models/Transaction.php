<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transaction';

    protected $fillable = [
        'montant',
        'type',
        'date',
        'description',
        'compte_id',
        'user_id',
        'categorie_id',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date' => 'date',
    ];

    public function compte()
    {
        return $this->belongsTo(Compte::class, 'compte_id');
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'user_id');
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function getFormattedMontantAttribute()
    {
        return number_format($this->montant, 2, ',', ' ') . ' €';
    }

    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->date)->format('d/m/Y');
    }

    public function isDepense()
    {
        return $this->type === 'dépense';
    }

    public function isRevenu()
    {
        return $this->type === 'revenu';
    }

    public function getMontantSigneAttribute()
    {
        return $this->isDepense() ? -$this->montant : $this->montant;
    }
}