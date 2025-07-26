<?php

namespace App\Modules\Student\Models;

use App\Modules\Student\Models\Student;
use App\Modules\ClassModel\Models\ClassModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'class_model_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class);
    }
}
