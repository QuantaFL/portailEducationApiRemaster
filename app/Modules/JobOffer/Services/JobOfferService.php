<?php

namespace App\Modules\JobOffer\Services;

use App\Modules\JobOffer\Exceptions\JobOfferException;
use App\Modules\JobOffer\Models\JobOffer;
use App\Modules\Subject\Models\Subject;
use App\Modules\User\Models\UserModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class JobOfferService
 *
 * Service pour la logique métier des offres d'emploi.
 */
class JobOfferService
{
    /**
     * Récupère toutes les offres d'emploi.
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllJobOffers(array $filters = []): LengthAwarePaginator
    {
        Log::info('JobOfferService: Récupération de toutes les offres d\'emploi', ['filters' => $filters]);

        $query = JobOffer::query();

        // Apply filters
        if (!empty($filters['subject_id'])) {
            $query->bySubject($filters['subject_id']);
        }

        if (!empty($filters['employment_type'])) {
            $query->byEmploymentType($filters['employment_type']);
        }

        if (!empty($filters['experience_level'])) {
            $query->byExperienceLevel($filters['experience_level']);
        }

        if (!empty($filters['location'])) {
            $query->where('location', 'LIKE', '%' . $filters['location'] . '%');
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['not_expired']) && $filters['not_expired']) {
            $query->notExpired();
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('description', 'LIKE', '%' . $search . '%')
                  ->orWhere('requirements', 'LIKE', '%' . $search . '%');
            });
        }

        // Ordering
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Récupère les offres d'emploi actives.
     *
     * @return Collection
     */
    public function getActiveJobOffers(): Collection
    {
        return JobOffer::active()->notExpired()->get();
    }

    /**
     * Récupère une offre d'emploi par son ID.
     *
     * @param int $id
     * @return JobOffer
     * @throws JobOfferException
     */
    public function getJobOfferById(int $id): JobOffer
    {
        $jobOffer = JobOffer::find($id);

        if (!$jobOffer) {
            throw JobOfferException::jobOfferNotFound();
        }

        return $jobOffer;
    }

    /**
     * Récupère une offre d'emploi par son numéro.
     *
     * @param string $offerNumber
     * @return JobOffer
     * @throws JobOfferException
     */
    public function getJobOfferByNumber(string $offerNumber): JobOffer
    {
        $jobOffer = JobOffer::where('offer_number', $offerNumber)->first();

        if (!$jobOffer) {
            throw JobOfferException::jobOfferNotFound();
        }

        return $jobOffer;
    }

    /**
     * Crée une nouvelle offre d'emploi.
     *
     * @param array $data
     * @return JobOffer
     * @throws JobOfferException
     */
    public function createJobOffer(array $data): JobOffer
    {
        Log::info('JobOfferService: Création d\'une nouvelle offre d\'emploi', [
            'title' => $data['title'] ?? null,
            'subject_id' => $data['subject_id'] ?? null
        ]);

        $this->validateJobOfferData($data);

        DB::beginTransaction();

        try {
            // Generate unique offer number
            $data['offer_number'] = $this->generateOfferNumber();
            Log::info('JobOfferService: Numéro d\'offre généré', ['offer_number' => $data['offer_number']]);

            // Set published_at if not provided and is_active is true
            if (!isset($data['published_at']) && ($data['is_active'] ?? false)) {
                $data['published_at'] = now();
            }

            $jobOffer = JobOffer::create($data);

            Log::info('JobOfferService: Offre d\'emploi créée avec succès', [
                'job_offer_id' => $jobOffer->id,
                'offer_number' => $jobOffer->offer_number,
                'title' => $jobOffer->title
            ]);

            DB::commit();

            return $jobOffer;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('JobOfferService: Échec de la création de l\'offre d\'emploi', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw JobOfferException::creationFailed();
        }
    }

    /**
     * Met à jour une offre d'emploi.
     *
     * @param JobOffer $jobOffer
     * @param array $data
     * @return JobOffer
     * @throws JobOfferException
     */
    public function updateJobOffer(JobOffer $jobOffer, array $data): JobOffer
    {
        Log::info('JobOfferService: Mise à jour de l\'offre d\'emploi', [
            'job_offer_id' => $jobOffer->id,
            'offer_number' => $jobOffer->offer_number
        ]);

        $this->validateJobOfferData($data, $jobOffer->id);

        DB::beginTransaction();

        try {
            // Handle published_at logic
            if (isset($data['is_active']) && $data['is_active'] && !$jobOffer->published_at) {
                $data['published_at'] = now();
            }

            $jobOffer->update($data);

            Log::info('JobOfferService: Offre d\'emploi mise à jour avec succès', [
                'job_offer_id' => $jobOffer->id
            ]);

            DB::commit();

            return $jobOffer->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('JobOfferService: Échec de la mise à jour de l\'offre d\'emploi', [
                'error' => $e->getMessage(),
                'job_offer_id' => $jobOffer->id
            ]);
            throw JobOfferException::updateFailed();
        }
    }

    /**
     * Supprime une offre d'emploi.
     *
     * @param JobOffer $jobOffer
     * @return bool
     * @throws JobOfferException
     */
    public function deleteJobOffer(JobOffer $jobOffer): bool
    {
        Log::info('JobOfferService: Suppression de l\'offre d\'emploi', [
            'job_offer_id' => $jobOffer->id,
            'offer_number' => $jobOffer->offer_number
        ]);

        try {
            // Check if there are applications
            if ($jobOffer->applications()->exists()) {
                throw JobOfferException::cannotDeleteWithApplications();
            }

            return $jobOffer->delete();
        } catch (\Exception $e) {
            Log::error('JobOfferService: Échec de la suppression de l\'offre d\'emploi', [
                'error' => $e->getMessage(),
                'job_offer_id' => $jobOffer->id
            ]);
            throw JobOfferException::deletionFailed();
        }
    }

    /**
     * Publie une offre d'emploi.
     *
     * @param JobOffer $jobOffer
     * @return JobOffer
     */
    public function publishJobOffer(JobOffer $jobOffer): JobOffer
    {
        Log::info('JobOfferService: Publication de l\'offre d\'emploi', [
            'job_offer_id' => $jobOffer->id,
            'offer_number' => $jobOffer->offer_number
        ]);

        $jobOffer->update([
            'is_active' => true,
            'published_at' => now()
        ]);

        return $jobOffer->fresh();
    }

    /**
     * Dépublie une offre d'emploi.
     *
     * @param JobOffer $jobOffer
     * @return JobOffer
     */
    public function unpublishJobOffer(JobOffer $jobOffer): JobOffer
    {
        Log::info('JobOfferService: Dépublication de l\'offre d\'emploi', [
            'job_offer_id' => $jobOffer->id,
            'offer_number' => $jobOffer->offer_number
        ]);

        $jobOffer->update(['is_active' => false]);

        return $jobOffer->fresh();
    }

    /**
     * Récupère les offres d'emploi par matière.
     *
     * @param int $subjectId
     * @return Collection
     * @throws JobOfferException
     */
    public function getJobOffersBySubject(int $subjectId): Collection
    {
        $subject = Subject::find($subjectId);
        if (!$subject) {
            throw JobOfferException::subjectNotFound();
        }

        return JobOffer::bySubject($subjectId)->active()->notExpired()->get();
    }

    /**
     * Valide les données de l'offre d'emploi.
     *
     * @param array $data
     * @param int|null $excludeId
     * @return void
     * @throws JobOfferException
     */
    private function validateJobOfferData(array $data, ?int $excludeId = null): void
    {
        // Vérifie que la matière existe
        if (isset($data['subject_id'])) {
            $subject = Subject::find($data['subject_id']);
            if (!$subject) {
                throw JobOfferException::subjectNotFound();
            }
        }

        // Vérifie que l'utilisateur qui publie l'offre existe
        if (isset($data['posted_by'])) {
            $user = UserModel::find($data['posted_by']);
            if (!$user) {
                throw JobOfferException::userNotFound();
            }
        }

        // Vérifie que la date limite est dans le futur
        if (isset($data['application_deadline'])) {
            $deadline = \Carbon\Carbon::parse($data['application_deadline']);
            if ($deadline->isPast()) {
                throw JobOfferException::invalidDeadline();
            }
        }

        // Vérifie que les salaires sont cohérents
        if (isset($data['salary_min'], $data['salary_max'])) {
            if ($data['salary_min'] > $data['salary_max']) {
                throw JobOfferException::invalidSalaryRange();
            }
        }

        // Vérifie que le type d'emploi est parmi les valeurs valides
        if (isset($data['employment_type'])) {
            $validTypes = ['temps_plein', 'temps_partiel', 'contrat'];
            if (!in_array($data['employment_type'], $validTypes)) {
                throw JobOfferException::invalidEmploymentType();
            }
        }

        // Vérifie que le niveau d'expérience est valide
        if (isset($data['experience_level'])) {
            $validLevels = ['debutant', 'junior', 'senior', 'expert'];
            if (!in_array($data['experience_level'], $validLevels)) {
                throw JobOfferException::invalidExperienceLevel();
            }
        }
    }

    /**
     * Génère un numéro d'offre unique.
     *
     * @return string
     */
    private function generateOfferNumber(): string
    {
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $attempt++;

            // Format: JOB-YYYY-MMDD-XXXX (JOB = Job Offer, YYYY = year, MMDD = month+day, XXXX = random)
            $year = date('Y');
            $monthDay = date('md');
            $randomNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $offerNumber = "JOB-{$year}-{$monthDay}-{$randomNumber}";

            // Check if this number already exists
            $exists = JobOffer::where('offer_number', $offerNumber)->exists();

            if (!$exists) {
                Log::info('JobOfferService: Numéro d\'offre unique généré', [
                    'offer_number' => $offerNumber,
                    'attempts' => $attempt
                ]);
                return $offerNumber;
            }

            Log::debug('JobOfferService: Le numéro d\'offre généré existe déjà, nouvelle tentative', [
                'offer_number' => $offerNumber,
                'attempt' => $attempt
            ]);

        } while ($attempt < $maxAttempts);

        // If we couldn't generate a unique number after max attempts, use timestamp-based approach
        $timestamp = time();
        $offerNumber = "JOB-{$year}-{$monthDay}-" . substr($timestamp, -4);

        Log::warning('JobOfferService: Utilisation d\'un numéro d\'offre basé sur l\'horodatage après le nombre maximum de tentatives', [
            'offer_number' => $offerNumber,
            'max_attempts_reached' => $maxAttempts
        ]);

        return $offerNumber;
    }

    /**
     * Récupère les statistiques des offres d'emploi.
     *
     * @return array
     */
    public function getJobOfferStatistics(): array
    {
        return [
            'total_offers' => JobOffer::count(),
            'active_offers' => JobOffer::active()->count(),
            'expired_offers' => JobOffer::where('application_deadline', '<', now()->toDateString())->count(),
            'draft_offers' => JobOffer::whereNull('published_at')->count(),
            'offers_by_subject' => JobOffer::select('subjects.name as subject_name', DB::raw('count(*) as total'))
                ->join('subjects', 'job_offers.subject_id', '=', 'subjects.id')
                ->groupBy('subjects.id', 'subjects.name')
                ->get()
                ->pluck('total', 'subject_name')
                ->toArray(),
            'offers_by_employment_type' => JobOffer::select('employment_type', DB::raw('count(*) as total'))
                ->groupBy('employment_type')
                ->get()
                ->pluck('total', 'employment_type')
                ->toArray(),
            'offers_by_experience_level' => JobOffer::select('experience_level', DB::raw('count(*) as total'))
                ->groupBy('experience_level')
                ->get()
                ->pluck('total', 'experience_level')
                ->toArray(),
        ];
    }
}
