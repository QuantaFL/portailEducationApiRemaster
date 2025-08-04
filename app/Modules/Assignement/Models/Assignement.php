<?php

namespace App\Modules\Assignement\Models;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Assignement
 *
 * @property int $id
 * @property int $teacher_id
 * @property int $class_model_id
 * @property int $subject_id
 * @property int $academic_year_id
 * @property array $day_of_week
 * @property string $start_time
 * @property string $end_time
 * @property float $coefficient
 * @property bool $isActive
 * @property string $assignment_number
 */
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

    /**
     * Récupère l'enseignant associé.
     *
     * @return BelongsTo
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Récupère la classe associée.
     *
     * @return BelongsTo
     */
    public function classModel(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class);
    }

    /**
     * Récupère la matière associée.
     *
     * @return BelongsTo
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Récupère l'année académique associée.
     *
     * @return BelongsTo
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
