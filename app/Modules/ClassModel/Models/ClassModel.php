<?php

namespace App\Modules\ClassModel\Models;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\Student\Models\StudentSession;
use App\Modules\Subject\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class ClassModel
 *
 * @property int $id
 * @property string $name
 * @property string $level
 */
class ClassModel extends Model
{
    use HasFactory;
    protected $with = ['currentAcademicYearStudentSessions'];

    protected $fillable = [
        'name',
        'level',
    ];

    /**
     * Récupère les sessions des étudiants pour l'année académique en cours.
     *
     * @return HasMany
     */
    public function currentAcademicYearStudentSessions(): HasMany
    {
        $currentAcademicYear = AcademicYear::getCurrentAcademicYear();

        if (!$currentAcademicYear) {
            return $this->hasMany(StudentSession::class)->whereRaw('1 = 0'); // Retourne une relation vide si aucune année académique en cours
        }

        return $this->hasMany(StudentSession::class)
            ->where('academic_year_id', $currentAcademicYear->id);
    }

    /**
     * Relation plusieurs-à-plusieurs avec Subject.
     *
     * @return BelongsToMany
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_model_id', 'subject_id')
                    ->withTimestamps();
    }
}
