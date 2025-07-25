<?php

namespace App\Modules\Assignement\Models;

use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Session\Models\Session;
use App\Modules\Subject\Models\Subject;
use App\modules\teacher\models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignement extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'class_model_id',
        'subject_id',
        'session_id',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classModel(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
}
