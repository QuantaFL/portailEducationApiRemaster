<?php

namespace App\Modules\Teacher\Models;

use App\Modules\User\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'hire_date',
        'user_model_id',
    ];
    protected $with = ['userModel'];

    public function userModel()
    {
        return $this->belongsTo(UserModel::class);
    }
}
