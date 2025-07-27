<?php

namespace App\Modules\Student\Models;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\Student\Models\Student;
use App\Modules\ClassModel\Models\ClassModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'class_model_id',
    ];


    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class);
    }
}
