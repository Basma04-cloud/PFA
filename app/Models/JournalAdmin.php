<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalAdmin extends Model
{
    use HasFactory;

    protected $table = 'journal_admin';

    protected $fillable = [
        'action',
        'type_action',
        'date_action',
        'user_id',
    ];

    protected $casts = [
        'date_action' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'user_id');
    }

    public static function logAction($userId, $action, $typeAction)
    {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'type_action' => $typeAction,
            'date_action' => now(),
        ]);
    }
}
