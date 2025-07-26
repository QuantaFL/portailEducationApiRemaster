<?php

namespace App\Modules\Assignement\Models;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Teacher;
use App\Modules\Term\Models\Term;
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
        'term_id',
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

    public function term()
    {
        return $this->belongsTo(Term::class);
    }
}
