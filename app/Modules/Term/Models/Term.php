<?php

namespace App\Modules\Term\Models;

use App\Modules\Session\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Term extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'academic_year_id',
    ];

    public function AcademicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
