<?php

namespace App\Modules\AcademicYear\Models;

use App\Modules\Term\Models\Term;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'start_date',
        'end_date',
        'status',
    ];

    public function terms()
    {
        return $this->hasMany(Term::class);
    }
}
