<?php

namespace App\Modules\Student\Models;

use App\Modules\Parent\Models\ParentModel;
use App\Modules\User\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'academic_records',
        'class_model_id',
        'parent_model_id',
        'user_model_id',
    ];

    public function parentModel(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class);
    }

    public function userModel()
    {
        return $this->belongsTo(UserModel::class);
    }
}
