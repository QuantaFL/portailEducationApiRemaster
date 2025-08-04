<?php

namespace App\Modules\JobOffer\Models;

use App\Modules\Subject\Models\Subject;
use App\Modules\User\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class JobOffer
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $requirements
 * @property int $subject_id
 * @property string $location
 * @property string $employment_type
 * @property float $salary_min
 * @property float $salary_max
 * @property string $experience_level
 * @property \Carbon\Carbon $application_deadline
 * @property bool $is_active
 * @property string $contact_email
 * @property string $contact_phone
 * @property string $benefits
 * @property string $offer_number
 * @property int $posted_by
 * @property \Carbon\Carbon $published_at
 */
class JobOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'requirements',
        'subject_id',
        'location',
        'employment_type',
        'salary_min',
        'salary_max',
        'experience_level',
        'application_deadline',
        'is_active',
        'contact_email',
        'contact_phone',
        'benefits',
        'offer_number',
        'posted_by',
        'published_at',
    ];

    protected $casts = [
        'application_deadline' => 'date',
        'published_at' => 'datetime',
        'is_active' => 'boolean',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
    ];

    protected $with = ['subject', 'postedBy'];

    /**
     * Récupère la matière associée.
     *
     * @return BelongsTo
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Récupère l'utilisateur qui a publié l'offre.
     *
     * @return BelongsTo
     */
    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'posted_by');
    }

    /**
     * Récupère les candidatures associées.
     *
     * @return HasMany
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Récupère les candidatures actives.
     *
     * @return HasMany
     */
    public function activeApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class)->where('status', '!=', 'rejected');
    }

    /**
     * Récupère les candidatures en attente.
     *
     * @return HasMany
     */
    public function pendingApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class)->where('status', 'pending');
    }

    /**
     * Filtre les offres actives.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Filtre les offres non expirées.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotExpired($query)
    {
        return $query->where('application_deadline', '>=', now()->toDateString());
    }

    /**
     * Filtre les offres par matière.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $subjectId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBySubject($query, int $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Filtre les offres par type d'emploi.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEmploymentType($query, string $type)
    {
        return $query->where('employment_type', $type);
    }

    /**
     * Filtre les offres par niveau d'expérience.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $level
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByExperienceLevel($query, string $level)
    {
        return $query->where('experience_level', $level);
    }

    /**
     * Vérifie si l'offre est expirée.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->application_deadline < now()->toDateString();
    }

    /**
     * Vérifie si l'offre est publiée.
     *
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }

    /**
     * Récupère le nombre de candidatures.
     *
     * @return int
     */
    public function getApplicationsCountAttribute(): int
    {
        return $this->applications()->count();
    }

    /**
     * Récupère le nombre de candidatures en attente.
     *
     * @return int
     */
    public function getPendingApplicationsCountAttribute(): int
    {
        return $this->pendingApplications()->count();
    }

    /**
     * Récupère la fourchette de salaire.
     *
     * @return string|null
     */
    public function getSalaryRangeAttribute(): ?string
    {
        if ($this->salary_min && $this->salary_max) {
            return number_format($this->salary_min, 0) . ' - ' . number_format($this->salary_max, 0) . ' EUR';
        }

        if ($this->salary_min) {
            return 'À partir de ' . number_format($this->salary_min, 0) . ' EUR';
        }

        if ($this->salary_max) {
            return 'Jusqu\'à ' . number_format($this->salary_max, 0) . ' EUR';
        }

        return null;
    }

    /**
     * Récupère le statut de l'offre.
     *
     * @return string
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->isExpired()) {
            return 'expired';
        }

        if (!$this->isPublished()) {
            return 'draft';
        }

        return 'active';
    }

    /**
     * Récupère le nombre de jours avant la date limite.
     *
     * @return int
     */
    public function getDaysUntilDeadlineAttribute(): int
    {
        return now()->diffInDays($this->application_deadline, false);
    }
}