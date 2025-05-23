<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compte extends Model
{
    use HasFactory;

    protected $table = 'compte';

    protected $fillable = [
        'nom_compte',
        'type',
        'user_id',
        'solde',
    ];

    protected $casts = [
        'solde' => 'decimal:2',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'compte_id');
    }

    public function getFormattedSoldeAttribute()
    {
        return number_format($this->solde, 2, ',', ' ') . ' €';
    }
}