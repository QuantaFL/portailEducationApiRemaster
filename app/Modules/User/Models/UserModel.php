<?php

namespace App\Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle représentant un utilisateur.
 *
 * Ce modèle gère les informations de l'utilisateur telles que le prénom, le nom, la date de naissance, l'email, le mot de passe, l'adresse et le téléphone.
 */
class UserModel extends Model
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
    ];
}
