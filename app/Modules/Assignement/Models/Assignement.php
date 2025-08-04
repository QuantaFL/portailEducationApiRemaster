<?php

namespace App\Modules\Assignement\Models;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignement extends Model
{
    use HasFactory;
    protected $table = 'assignments';

    protected $fillable = [
        'teacher_id',
        'class_model_id',
        'subject_id',
        'academic_year_id',
        'day_of_week',
        'start_time',
        'end_time',
        'coefficient',
        'isActive',
        'assignment_number',
    ];

    protected $with = ['teacher', 'subject', 'academicYear', 'classModel'];

    protected $casts = [
        'coefficient' => 'float',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'isActive' => 'boolean',
        'day_of_week' => 'array',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
