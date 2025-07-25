<?php

namespace App\Modules\Grade\Models;

use App\Modules\Assignement\Models\Assignement;
use App\Modules\Student\Models\Student;
use App\Modules\Term\Models\Term;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'mark',
        'assignement_id',
        'student_id',
        'term_id',
    ];

    public function assignement(): BelongsTo
    {
        return $this->belongsTo(Assignement::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
