<?php

namespace App\Modules\Parent\Models;

use App\Modules\User\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_model_id',
    ];
    protected $with = ['UserModel'];

    public function UserModel()
    {
        return $this->belongsTo(UserModel::class);
    }
}
