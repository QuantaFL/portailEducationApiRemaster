<?php

namespace App\Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleModel extends Model
{
    use HasFactory;

    protected $table = 'role_models';
    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(UserModel::class, 'role_id');
    }
}

