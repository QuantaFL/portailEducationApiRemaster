<?php

namespace App\Modules\JobOffer\Resources;

use App\Modules\JobOffer\Models\JobApplication;
use App\Modules\User\Ressources\UserModelResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin JobApplication */
class JobApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'applicant_first_name' => $this->applicant_first_name,
            'applicant_last_name' => $this->applicant_last_name,
            'applicant_full_name' => $this->applicant_full_name,
            'applicant_email' => $this->applicant_email,
            'applicant_phone' => $this->applicant_phone,
            'cover_letter' => $this->cover_letter,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'admin_notes' => $this->admin_notes,
            'application_number' => $this->application_number,
            'applied_at' => $this->applied_at->toISOString(),
            'applied_at_formatted' => $this->applied_at->format('d/m/Y H:i'),
            'days_ago' => $this->days_ago,
            'reviewed_at' => $this->reviewed_at?->toISOString(),
            'reviewed_at_formatted' => $this->reviewed_at?->format('d/m/Y H:i'),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // File information
            'has_files' => $this->hasFiles(),
            'has_cover_letter_file' => $this->hasCoverLetterFile(),
            'cv_original_name' => $this->cv_original_name,
            'cover_letter_original_name' => $this->cover_letter_original_name,
            'formatted_file_size' => $this->formatted_file_size,
            
            // Status flags
            'is_reviewed' => $this->isReviewed(),
            'can_be_modified' => $this->canBeModified(),
            
            // Download URLs
            'cv_download_url' => $this->hasFiles() ? "/api/v1/job-applications/{$this->id}/download/cv" : null,
            'cover_letter_download_url' => $this->hasCoverLetterFile() ? "/api/v1/job-applications/{$this->id}/download/cover_letter" : null,
            
            // Relations
            'job_offer' => new JobOfferResource($this->whenLoaded('jobOffer')),
            'reviewed_by' => new UserModelResource($this->whenLoaded('reviewedBy')),
        ];
    }
}