<?php

namespace App\Modules\AcademicYear\Models;

use App\Modules\Term\Models\Term;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class AcademicYear
 *
 * @property int $id
 * @property string $label
 * @property int $start_date
 * @property int $end_date
 * @property string $status
 */
class AcademicYear extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'label',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * Récupère l'année académique en cours.
     *
     * @return AcademicYear|null
     */
    public static function getCurrentAcademicYear(): ?AcademicYear
    {
        return self::where('status', 'en_cours')->first();
    }

    /**
     * Récupère les semestres de l'année académique.
     *
     * @return HasMany
     */
    public function terms(): HasMany
    {
        return $this->hasMany(Term::class);
    }
}
