<?php

namespace App\Modules\ReportCard\Models;

use App\Modules\Student\Models\StudentSession;
use App\Modules\Term\Models\Term;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'average_grade',
        'honors',
        'student_session_id',
        'term_id',
        'path',
        'rank',
    ];

    protected $appends = ['pdf_url'];

    public function getPdfUrlAttribute(): ?string
    {
        if ($this->path) {
            return asset('storage/' . str_replace('public/', '', $this->path));
        }
        return null;
    }

    public function studentSession(): BelongsTo
    {
        return $this->belongsTo(StudentSession::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
