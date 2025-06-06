<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class Compte extends Model
{
    use HasFactory;

    // Spécifier explicitement le nom de la table
    protected $table = 'compte';

    // Champs fillable de base
    protected $fillable = [
        'nom_compte',
        'type_compte',
        'solde',
        'user_id',
    ];

    protected $casts = [
        'solde' => 'decimal:2'
    ];

    protected $attributes = [
        'solde' => 0
    ];

    // Constructeur pour ajouter dynamiquement les champs selon la structure
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        // Ajouter 'description' aux fillable si la colonne existe
        if (Schema::hasColumn('compte', 'description')) {
            $this->fillable[] = 'description';
        }
    }

    // Relation avec l'utilisateur - CORRIGÉE pour pointer vers 'users'
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relation avec les transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'compte_id');
    }

    // Accesseur pour le solde formaté
    public function getSoldeFormatteAttribute()
    {
        return number_format($this->solde, 2, ',', ' ') . ' €';
    }

    // Accesseur pour le nom formaté du type
    public function getTypeFormatteAttribute()
    {
        $types = [
            'courant' => 'Compte Courant',
            'epargne' => 'Compte Épargne',
            'credit' => 'Compte Crédit',
            'investissement' => 'Compte Investissement'
        ];

        return $types[$this->type_compte] ?? ucfirst($this->type_compte);
    }

    // Validation pour les données du formulaire (SANS user_id)
    public static function getValidationRules()
    {
        $rules = [
            'nom_compte' => 'required|string|max:255',
            'type_compte' => 'required|in:courant,epargne,credit,investissement',
            'solde' => 'nullable|numeric|min:0',
        ];

        // Ajouter la validation pour description si la colonne existe
        if (Schema::hasColumn('compte', 'description')) {
            $rules['description'] = 'nullable|string|max:1000';
        }

        return $rules;
    }

    // Validation pour les données complètes (AVEC user_id) - pour usage interne
    public static function getFullValidationRules()
    {
        $rules = self::getValidationRules();
        $rules['user_id'] = 'required|exists:users,id'; // CORRIGÉ: users au lieu d'utilisateur
        
        return $rules;
    }

    // Scope pour les comptes d'un utilisateur
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope pour un type de compte
    public function scopeOfType($query, $type)
    {
        return $query->where('type_compte', $type);
    }

    // Méthode pour créer un compte avec user_id automatique
    public static function createForUser($data, $userId)
    {
        // Vérifier que l'utilisateur existe dans la table 'users'
        $user = \App\Models\User::find($userId);
        if (!$user) {
            throw new \Exception("Utilisateur avec l'ID {$userId} non trouvé dans la table 'users'");
        }
        
        $data['user_id'] = $userId;
        
        Log::info('Création de compte pour utilisateur', [
            'user_id' => $userId,
            'user_name' => $user->name,
            'data' => $data
        ]);
        
        return self::create($data);
    }

    // Méthode pour vérifier la cohérence des données
    public static function verifierCoherence()
    {
        $stats = [
            'table_compte_exists' => Schema::hasTable('compte'),
            'table_users_exists' => Schema::hasTable('users'),
            'table_utilisateur_exists' => Schema::hasTable('utilisateur'),
            'total_comptes' => 0,
            'comptes_avec_user_valide' => 0,
            'comptes_avec_user_invalide' => 0
        ];

        if ($stats['table_compte_exists']) {
            $stats['total_comptes'] = self::count();
            
            // Vérifier les user_id valides
            $stats['comptes_avec_user_valide'] = self::whereExists(function ($query) {
                $query->select(\DB::raw(1))
                      ->from('users')
                      ->whereColumn('users.id', 'compte.user_id');
            })->count();
            
            $stats['comptes_avec_user_invalide'] = $stats['total_comptes'] - $stats['comptes_avec_user_valide'];
        }

        return $stats;
    }
}
