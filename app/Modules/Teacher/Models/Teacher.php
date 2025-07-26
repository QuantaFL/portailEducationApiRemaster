<?php

namespace App\Modules\Teacher\Models;

use App\Modules\Subject\Models\Subject;
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
    protected $with = ['userModel', 'subjects'];

    public function userModel()
    {
        return $this->belongsTo(UserModel::class);
    }
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects');
    }

    public function assignedClasses()
    {
        return $this->belongsToMany(
            \App\Modules\ClassModel\Models\ClassModel::class,
            'assignments', // Table pivot
            'teacher_id',  // Clé étrangère sur la table pivot (assignments) qui lie au modèle Teacher
            'class_model_id' // Clé étrangère sur la table pivot (assignments) qui lie au modèle ClassModel
        )->whereHas('terms.academicYear', function ($query) {
            $query->where('status', \App\Modules\AcademicYear\Models\StatusAcademicYearEnum::EN_COURS->value);
        });
    }
}
