<?php

namespace App\Modules\ClassModel\Models;

use App\Modules\AcademicYear\Models\StatusAcademicYearEnum;
use App\Modules\Student\Models\StudentSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassModel extends Model
{
    use HasFactory;
    protected $with = ['latestStudentSession'];

    protected $fillable = [
        'name',
        'level',
    ];

    public function latestStudentSession(): HasMany
    {
        $currentAcademicYear = StudentSession::where('status', 'active')
            ->orderByDesc('academic_year')
            ->value('academic_year');

        return $this->hasMany(StudentSession::class)
            ->where('academic_year', $currentAcademicYear)
            ->where('status', StatusAcademicYearEnum::EN_COURS->value);
    }
}
