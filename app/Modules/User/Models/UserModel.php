<?php

namespace App\Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Modèle représentant un utilisateur.
 *
 * Ce modèle gère les informations de l'utilisateur telles que le prénom, le nom, la date de naissance, l'email, le mot de passe, l'adresse et le téléphone.
 */
class UserModel extends Authenticatable implements JWTSubject
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'birthday',
        'email',
        'password',
        'adress',
        'phone',
        'role_id', // Ajouté pour permettre l'enregistrement du rôle
        'isFirstLogin',
        'gender',
        'nationality'
    ];

    protected $casts = [
        'isFirstLogin' => 'boolean',
    ];

    // Méthodes requises par JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'birthday' => $this->birthday,
            'email' => $this->email,
            'adress' => $this->adress,
            'phone' => $this->phone,
            'role' => $this->role ? $this->role->name : null,
            'isFirstLogin' => $this->isFirstLogin,
            'gender'=>$this->gender ? $this->gender:null
        ];
    }

    public function role()
    {
        return $this->belongsTo(RoleModel::class, 'role_id');
    }
}
