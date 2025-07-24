<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_model_id',
    ];

    public function userModel(): BelongsTo
    {
        return $this->belongsTo(UserModel::class);
    }
}
