<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $table = 'categorie';

    protected $fillable = [
        'nom_categorie',
        'type',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'categorie_id');
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class, 'categorie_id');
    }

    public function isDepense()
    {
        return $this->type === 'dépense';
    }

    public function isRevenu()
    {
        return $this->type === 'revenu';
    }
}