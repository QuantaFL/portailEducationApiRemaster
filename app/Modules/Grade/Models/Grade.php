<?php

namespace App\Modules\Grade\Models;

use App\Modules\Assignement\Models\Assignement;
use App\Modules\Student\Models\StudentSession;
use App\Modules\Term\Models\Term;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function assignment()
    {
        return $this->belongsTo(Assignement::class);
    }

    public function studentSession()
    {
        return $this->belongsTo(StudentSession::class);
    }

    public function term() : BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
