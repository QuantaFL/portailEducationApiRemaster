<?php

namespace App\Modules\Teacher\Models;

use App\Modules\Subject\Models\Subject;
use App\Modules\User\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'hire_date',
        'user_model_id',
    ];

    protected $with = ['userModel'];
    public function userModel(): BelongsTo
    {
        return $this->belongsTo(UserModel::class);
    }

    public function specialities()
    {
        return $this->hasMany(Subject::class);
    }
}
