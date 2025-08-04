<?php

namespace App\Modules\JobOffer\Resources;

use App\Modules\JobOffer\Models\JobOffer;
use App\Modules\Subject\Ressources\SubjectResource;
use App\Modules\User\Ressources\UserModelResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin JobOffer */
class JobOfferResource extends JsonResource
{
    /**
     * Transforme la ressource en un tableau.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'location' => $this->location,
            'employment_type' => $this->employment_type,
            'employment_type_label' => $this->getEmploymentTypeLabel(),
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'salary_range' => $this->salary_range,
            'experience_level' => $this->experience_level,
            'experience_level_label' => $this->getExperienceLevelLabel(),
            'application_deadline' => $this->application_deadline->format('Y-m-d'),
            'application_deadline_formatted' => $this->application_deadline->format('d/m/Y'),
            'days_until_deadline' => $this->days_until_deadline,
            'is_active' => $this->is_active,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'benefits' => $this->benefits,
            'offer_number' => $this->offer_number,
            'published_at' => $this->published_at?->toISOString(),
            'published_at_formatted' => $this->published_at?->format('d/m/Y H:i'),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'applications_count' => $this->applications_count,
            'pending_applications_count' => $this->pending_applications_count,
            'is_expired' => $this->isExpired(),
            'is_published' => $this->isPublished(),

            // Relations
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'posted_by' => new UserModelResource($this->whenLoaded('postedBy')),
            'applications' => JobApplicationResource::collection($this->whenLoaded('applications')),
        ];
    }

    /**
     * Récupère le libellé du type d'emploi.
     *
     * @return string
     */
    private function getEmploymentTypeLabel(): string
    {
        return match ($this->employment_type) {
            'full_time' => 'Temps plein',
            'part_time' => 'Temps partiel',
            'contract' => 'Contrat',
            default => 'Non spécifié'
        };
    }

    /**
     * Récupère le libellé du niveau d'expérience.
     *
     * @return string
     */
    private function getExperienceLevelLabel(): string
    {
        return match ($this->experience_level) {
            'entry' => 'Débutant',
            'junior' => 'Junior',
            'senior' => 'Senior',
            'expert' => 'Expert',
            default => 'Non spécifié'
        };
    }

    /**
     * Récupère le libellé du statut.
     *
     * @return string
     */
    private function getStatusLabel(): string
    {
        return match ($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'expired' => 'Expirée',
            'draft' => 'Brouillon',
            default => 'Inconnu'
        };
    }
}
