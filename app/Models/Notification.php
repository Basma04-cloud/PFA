<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notification';

    protected $fillable = [
        'message',
        'lu',
        'date_envoi',
        'user_id',
    ];

    protected $casts = [
        'lu' => 'boolean',
        'date_envoi' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'user_id');
    }

    public function marquerCommeLu()
    {
        $this->lu = true;
        $this->save();
    }

    public function getTimeAgoAttribute()
    {
        return $this->date_envoi->diffForHumans();
    }
}
