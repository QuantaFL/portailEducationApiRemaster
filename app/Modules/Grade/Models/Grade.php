<?php

namespace App\Modules\Grade\Models;

use App\Modules\Assignement\Models\Assignement;
use App\Modules\Student\Models\StudentSession;
use App\Modules\Subject\Models\Subject;
use App\Modules\Term\Models\Term;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Grade
 *
 * @property int $id
 * @property float $mark
 * @property string $type
 * @property int $assignement_id
 * @property int $student_session_id
 * @property int $term_id
 */
class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'mark',
        'type',
        'assignement_id',
        'student_session_id',
        'term_id',
    ];

    protected $with = ['assignement', 'studentSession', 'term', 'subject'];

    /**
     * Récupère l'affectation associée.
     *
     * @return BelongsTo
     */
    public function assignement(): BelongsTo
    {
        return $this->belongsTo(Assignement::class);
    }

    /**
     * Récupère la session de l'étudiant associée.
     *
     * @return BelongsTo
     */
    public function studentSession(): BelongsTo
    {
        return $this->belongsTo(StudentSession::class);
    }

    /**
     * Récupère le semestre associé.
     *
     * @return BelongsTo
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
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
}
