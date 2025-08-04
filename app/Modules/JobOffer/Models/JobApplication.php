<?php

namespace App\Modules\JobOffer\Models;

use App\Modules\User\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function jobOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'reviewed_by');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByJobOffer($query, $jobOfferId)
    {
        return $query->where('job_offer_id', $jobOfferId);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('applied_at', '>=', now()->subDays($days));
    }

    public function getApplicantFullNameAttribute(): string
    {
        return $this->applicant_first_name . ' ' . $this->applicant_last_name;
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'reviewed' => 'Examinée',
            'accepted' => 'Acceptée',
            'rejected' => 'Refusée',
            default => 'Inconnu'
        };
    }

    public function getDaysAgoAttribute(): int
    {
        return $this->applied_at->diffInDays(now());
    }

    public function hasFiles(): bool
    {
        return !empty($this->cv_path);
    }

    public function hasCoverLetterFile(): bool
    {
        return !empty($this->cover_letter_path);
    }

    public function getCvFileSize(): ?int
    {
        if ($this->cv_path && file_exists(storage_path('app/' . $this->cv_path))) {
            return filesize(storage_path('app/' . $this->cv_path));
        }
        return null;
    }

    public function getCoverLetterFileSize(): ?int
    {
        if ($this->cover_letter_path && file_exists(storage_path('app/' . $this->cover_letter_path))) {
            return filesize(storage_path('app/' . $this->cover_letter_path));
        }
        return null;
    }

    public function getFormattedFileSizeAttribute(): ?string
    {
        $size = $this->getCvFileSize();
        if (!$size) return null;
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = floor(log($size, 1024));
        return round($size / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    public function isReviewed(): bool
    {
        return in_array($this->status, ['reviewed', 'accepted', 'rejected']);
    }

    public function canBeModified(): bool
    {
        return $this->status === 'pending';
    }
}