<?php

namespace App\Modules\Term\Models;

use App\Modules\AcademicYear\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'academic_year_id',
        'start_date',
        'end_date',
        'status',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}