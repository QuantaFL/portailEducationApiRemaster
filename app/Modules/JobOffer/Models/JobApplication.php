<?php

namespace App\Modules\JobOffer\Models;

use App\Modules\User\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class JobApplication
 *
 * @property int $id
 * @property int $job_offer_id
 * @property string $applicant_first_name
 * @property string $applicant_last_name
 * @property string $applicant_email
 * @property string $applicant_phone
 * @property string $cover_letter
 * @property string $cv_path
 * @property string $cv_original_name
 * @property string $cover_letter_path
 * @property string $cover_letter_original_name
 * @property string $status
 * @property string $admin_notes
 * @property string $application_number
 * @property \Carbon\Carbon $applied_at
 * @property \Carbon\Carbon $reviewed_at
 * @property int $reviewed_by
 */
class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_offer_id',
        'applicant_first_name',
        'applicant_last_name',
        'applicant_email',
        'applicant_phone',
        'cover_letter',
        'cv_path',
        'cv_original_name',
        'cover_letter_path',
        'cover_letter_original_name',
        'status',
        'admin_notes',
        'application_number',
        'applied_at',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    protected $with = ['jobOffer', 'reviewedBy'];

    /**
     * Récupère l'offre d'emploi associée.
     *
     * @return BelongsTo
     */
    public function jobOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class);
    }

    /**
     * Récupère l'utilisateur qui a examiné la candidature.
     *
     * @return BelongsTo
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'reviewed_by');
    }

    /**
     * Filtre les candidatures par statut.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Filtre les candidatures en attente.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Filtre les candidatures examinées.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    /**
     * Filtre les candidatures acceptées.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Filtre les candidatures rejetées.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Filtre les candidatures par offre d'emploi.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $jobOfferId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByJobOffer($query, int $jobOfferId)
    {
        return $query->where('job_offer_id', $jobOfferId);
    }

    /**
     * Filtre les candidatures récentes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('applied_at', '>=', now()->subDays($days));
    }

    /**
     * Récupère le nom complet du candidat.
     *
     * @return string
     */
    public function getApplicantFullNameAttribute(): string
    {
        return $this->applicant_first_name . ' ' . $this->applicant_last_name;
    }

    /**
     * Récupère le libellé du statut.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'En attente',
            'reviewed' => 'Examinée',
            'accepted' => 'Acceptée',
            'rejected' => 'Refusée',
            default => 'Inconnu'
        };
    }

    /**
     * Récupère le nombre de jours écoulés depuis la candidature.
     *
     * @return int
     */
    public function getDaysAgoAttribute(): int
    {
        return $this->applied_at->diffInDays(now());
    }

    /**
     * Vérifie si la candidature a des fichiers.
     *
     * @return bool
     */
    public function hasFiles(): bool
    {
        return !empty($this->cv_path);
    }

    /**
     * Vérifie si la candidature a une lettre de motivation.
     *
     * @return bool
     */
    public function hasCoverLetterFile(): bool
    {
        return !empty($this->cover_letter_path);
    }

    /**
     * Récupère la taille du fichier CV.
     *
     * @return int|null
     */
    public function getCvFileSize(): ?int
    {
        if ($this->cv_path && file_exists(storage_path('app/' . $this->cv_path))) {
            return filesize(storage_path('app/' . $this->cv_path));
        }
        return null;
    }

    /**
     * Récupère la taille du fichier de la lettre de motivation.
     *
     * @return int|null
     */
    public function getCoverLetterFileSize(): ?int
    {
        if ($this->cover_letter_path && file_exists(storage_path('app/' . $this->cover_letter_path))) {
            return filesize(storage_path('app/' . $this->cover_letter_path));
        }
        return null;
    }

    /**
     * Récupère la taille formatée du fichier.
     *
     * @return string|null
     */
    public function getFormattedFileSizeAttribute(): ?string
    {
        $size = $this->getCvFileSize();
        if (!$size) return null;

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = floor(log($size, 1024));
        return round($size / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    /**
     * Vérifie si la candidature a été examinée.
     *
     * @return bool
     */
    public function isReviewed(): bool
    {
        return in_array($this->status, ['reviewed', 'accepted', 'rejected']);
    }

    /**
     * Vérifie si la candidature peut être modifiée.
     *
     * @return bool
     */
    public function canBeModified(): bool
    {
        return $this->status === 'pending';
    }
}
