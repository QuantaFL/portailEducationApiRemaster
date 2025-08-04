<?php

namespace App\Modules\JobOffer\Models;

use App\Modules\Subject\Models\Subject;
use App\Modules\User\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'posted_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function activeApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class)->where('status', '!=', 'rejected');
    }

    public function pendingApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class)->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where('application_deadline', '>=', now()->toDateString());
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    public function scopeByExperienceLevel($query, $level)
    {
        return $query->where('experience_level', $level);
    }

    public function isExpired(): bool
    {
        return $this->application_deadline < now()->toDateString();
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }

    public function getApplicationsCountAttribute(): int
    {
        return $this->applications()->count();
    }

    public function getPendingApplicationsCountAttribute(): int
    {
        return $this->pendingApplications()->count();
    }

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

    public function getDaysUntilDeadlineAttribute(): int
    {
        return now()->diffInDays($this->application_deadline, false);
    }
}