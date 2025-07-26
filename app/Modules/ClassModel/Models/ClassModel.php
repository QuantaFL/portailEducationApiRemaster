<?php

namespace App\Modules\ClassModel\Models;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Models\StatusAcademicYearEnum;
use App\Modules\Student\Models\StudentSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassModel extends Model
{
    use HasFactory;
    protected $with = ['currentAcademicYearStudentSessions'];

    protected $fillable = [
        'name',
        'level',
    ];

    public function currentAcademicYearStudentSessions(): HasMany
    {
        $currentAcademicYear = AcademicYear::getCurrentAcademicYear();

        if (!$currentAcademicYear) {
            return $this->hasMany(StudentSession::class)->whereRaw('1 = 0'); // Return empty relation if no current academic year
        }

        return $this->hasMany(StudentSession::class)
            ->where('academic_year_id', $currentAcademicYear->id);
    }
}
