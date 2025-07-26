<?php

namespace App\Modules\Student\Models;

use App\Modules\Parent\Models\ParentModel;
use App\Modules\User\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'academic_records',
        'parent_model_id',
        'user_model_id',
    ];
    protected $with = [
        'parentModel',
        'userModel',
        'latestStudentSession',
    ];

    public function parentModel(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class);
    }

    public function userModel()
    {
        return $this->belongsTo(UserModel::class);
    }

    public function studentSession()
    {
        return $this->hasMany(StudentSession::class);
    }

    public function latestStudentSession()
    {
        return $this->hasOne(StudentSession::class)->latestOfMany();
    }

    public function getAcademicRecordsUrlAttribute()
    {
        return $this->academic_records ? Storage::disk('public')->url($this->academic_records) : null;
    }
}
