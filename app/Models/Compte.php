<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compte extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'type',
        'solde',
        'description',
        'user_id'
    ];

    protected $casts = [
        'solde' => 'decimal:2'
    ];

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec les transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
