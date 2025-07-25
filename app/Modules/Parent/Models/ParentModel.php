<?php

namespace App\Modules\Parent\Models;

use App\Modules\User\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_model_id',
    ];
    protected $with = ['userModel'];

    public function UserModel(): BelongsTo
    {
        return $this->belongsTo(UserModel::class);
    }
}
