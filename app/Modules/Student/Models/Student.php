<?php

namespace App\Modules\Student\Models;

use App\Modules\ClassModel\Models\ClassModel;
use App\Modules\Parent\Models\ParentModel;
use App\Modules\Session\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'parent_id',
        'user_model_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class);
    }

    public function sessions(): BelongsToMany
    {
        return $this->belongsToMany(AcademicYear::class, 'student_session', 'student_id', 'session_id');
    }
}
