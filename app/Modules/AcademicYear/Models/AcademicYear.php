<?php

namespace App\Modules\AcademicYear\Models;

use App\Modules\Student\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'start_date',
        'end_date',
        'status',
    ];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_session', 'session_id', 'student_id');
    }
}
