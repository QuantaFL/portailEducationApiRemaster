<?php

namespace App\Modules\JobOffer\Services;

use App\Modules\JobOffer\Exceptions\JobOfferException;
use App\Modules\JobOffer\Models\JobApplication;
use App\Modules\JobOffer\Models\JobOffer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class JobApplicationService
 *
 * Service pour la logique métier des candidatures.
 */
class JobApplicationService
{
    private const ALLOWED_CV_EXTENSIONS = ['pdf', 'doc', 'docx'];
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    private const UPLOAD_DIRECTORY = 'job-applications';

    /**
     * Récupère toutes les candidatures.
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllApplications(array $filters = []): LengthAwarePaginator
    {
        Log::info('JobApplicationService: Récupération de toutes les candidatures', ['filters' => $filters]);

        $query = JobApplication::query();

        // Apply filters
        if (!empty($filters['job_offer_id'])) {
            $query->byJobOffer($filters['job_offer_id']);
        }

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (!empty($filters['applicant_email'])) {
            $query->where('applicant_email', 'LIKE', '%' . $filters['applicant_email'] . '%');
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('applicant_first_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('applicant_last_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('applicant_email', 'LIKE', '%' . $search . '%')
                  ->orWhere('application_number', 'LIKE', '%' . $search . '%');
            });
        }

        if (!empty($filters['recent_days'])) {
            $query->recent($filters['recent_days']);
        }

        // Ordering
        $orderBy = $filters['order_by'] ?? 'applied_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Récupère une candidature par son ID.
     *
     * @param int $id
     * @return JobApplication
     * @throws JobOfferException
     */
    public function getApplicationById(int $id): JobApplication
    {
        $application = JobApplication::find($id);

        if (!$application) {
            throw JobOfferException::applicationNotFound();
        }

        return $application;
    }

    /**
     * Récupère une candidature par son numéro.
     *
     * @param string $applicationNumber
     * @return JobApplication
     * @throws JobOfferException
     */
    public function getApplicationByNumber(string $applicationNumber): JobApplication
    {
        $application = JobApplication::where('application_number', $applicationNumber)->first();

        if (!$application) {
            throw JobOfferException::applicationNotFound();
        }

        return $application;
    }

    /**
     * Crée une nouvelle candidature.
     *
     * @param array $data
     * @param UploadedFile|null $cvFile
     * @param UploadedFile|null $coverLetterFile
     * @return JobApplication
     * @throws JobOfferException
     */
    public function createApplication(array $data, ?UploadedFile $cvFile = null, ?UploadedFile $coverLetterFile = null): JobApplication
    {
        Log::info('JobApplicationService: Création d\'une nouvelle candidature', [
            'job_offer_id' => $data['job_offer_id'] ?? null,
            'applicant_email' => $data['applicant_email'] ?? null,
            'has_cv_file' => $cvFile !== null,
            'has_cover_letter_file' => $coverLetterFile !== null
        ]);

        $this->validateApplicationData($data);
        $this->validateJobOfferForApplication($data['job_offer_id']);

        DB::beginTransaction();

        try {
            // Generate unique application number
            $data['application_number'] = $this->generateApplicationNumber();
            Log::info('JobApplicationService: Numéro de candidature généré', ['application_number' => $data['application_number']]);

            // Handle CV file upload (required)
            if (!$cvFile) {
                throw JobOfferException::cvFileRequired();
            }

            $cvUploadResult = $this->uploadFile($cvFile, 'cv', $data['application_number']);
            $data['cv_path'] = $cvUploadResult['path'];
            $data['cv_original_name'] = $cvUploadResult['original_name'];

            // Handle cover letter file upload (optional)
            if ($coverLetterFile) {
                $coverLetterUploadResult = $this->uploadFile($coverLetterFile, 'cover_letter', $data['application_number']);
                $data['cover_letter_path'] = $coverLetterUploadResult['path'];
                $data['cover_letter_original_name'] = $coverLetterUploadResult['original_name'];
            }

            // Set applied_at timestamp
            $data['applied_at'] = now();

            $application = JobApplication::create($data);

            Log::info('JobApplicationService: Candidature créée avec succès', [
                'application_id' => $application->id,
                'application_number' => $application->application_number,
                'job_offer_id' => $application->job_offer_id
            ]);

            DB::commit();

            return $application;

        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded files in case of error
            if (isset($cvUploadResult['path'])) {
                Storage::delete($cvUploadResult['path']);
            }
            if (isset($coverLetterUploadResult['path'])) {
                Storage::delete($coverLetterUploadResult['path']);
            }

            Log::error('JobApplicationService: Échec de la création de la candidature', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw JobOfferException::applicationCreationFailed();
        }
    }

    /**
     * Met à jour le statut d'une candidature.
     *
     * @param JobApplication $application
     * @param string $status
     * @param int|null $reviewedBy
     * @param string|null $adminNotes
     * @return JobApplication
     * @throws JobOfferException
     */
    public function updateApplicationStatus(JobApplication $application, string $status, ?int $reviewedBy = null, ?string $adminNotes = null): JobApplication
    {
        Log::info('JobApplicationService: Mise à jour du statut de la candidature', [
            'application_id' => $application->id,
            'old_status' => $application->status,
            'new_status' => $status,
            'reviewed_by' => $reviewedBy
        ]);

        $validStatuses = ['pending', 'reviewed', 'accepted', 'rejected'];
        if (!in_array($status, $validStatuses)) {
            throw JobOfferException::invalidApplicationStatus();
        }

        DB::beginTransaction();

        try {
            $updateData = [
                'status' => $status,
                'reviewed_at' => now(),
                'reviewed_by' => $reviewedBy,
            ];

            if ($adminNotes !== null) {
                $updateData['admin_notes'] = $adminNotes;
            }

            $application->update($updateData);

            Log::info('JobApplicationService: Statut de la candidature mis à jour avec succès', [
                'application_id' => $application->id,
                'new_status' => $status
            ]);

            DB::commit();

            return $application->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('JobApplicationService: Échec de la mise à jour du statut de la candidature', [
                'error' => $e->getMessage(),
                'application_id' => $application->id
            ]);
            throw JobOfferException::applicationUpdateFailed();
        }
    }

    /**
     * Supprime une candidature.
     *
     * @param JobApplication $application
     * @return bool
     * @throws JobOfferException
     */
    public function deleteApplication(JobApplication $application): bool
    {
        Log::info('JobApplicationService: Suppression de la candidature', [
            'application_id' => $application->id,
            'application_number' => $application->application_number
        ]);

        try {
            // Delete associated files
            if ($application->cv_path) {
                Storage::delete($application->cv_path);
            }
            if ($application->cover_letter_path) {
                Storage::delete($application->cover_letter_path);
            }

            return $application->delete();
        } catch (\Exception $e) {
            Log::error('JobApplicationService: Échec de la suppression de la candidature', [
                'error' => $e->getMessage(),
                'application_id' => $application->id
            ]);
            throw JobOfferException::applicationDeletionFailed();
        }
    }

    /**
     * Télécharge un fichier d'une candidature.
     *
     * @param JobApplication $application
     * @param string $fileType
     * @return array
     * @throws JobOfferException
     */
    public function downloadFile(JobApplication $application, string $fileType): array
    {
        Log::info('JobApplicationService: Téléchargement du fichier', [
            'application_id' => $application->id,
            'file_type' => $fileType
        ]);

        $filePath = null;
        $originalName = null;

        switch ($fileType) {
            case 'cv':
                $filePath = $application->cv_path;
                $originalName = $application->cv_original_name;
                break;
            case 'cover_letter':
                $filePath = $application->cover_letter_path;
                $originalName = $application->cover_letter_original_name;
                break;
            default:
                throw JobOfferException::invalidFileType();
        }

        if (!$filePath || !Storage::exists($filePath)) {
            throw JobOfferException::fileNotFound();
        }

        return [
            'path' => storage_path('app/' . $filePath),
            'original_name' => $originalName,
            'mime_type' => Storage::mimeType($filePath),
            'size' => Storage::size($filePath)
        ];
    }

    /**
     * Récupère les candidatures par offre d'emploi.
     *
     * @param int $jobOfferId
     * @return Collection
     */
    public function getApplicationsByJobOffer(int $jobOfferId): Collection
    {
        return JobApplication::byJobOffer($jobOfferId)->orderBy('applied_at', 'desc')->get();
    }

    /**
     * Récupère les candidatures récentes.
     *
     * @param int $days
     * @return Collection
     */
    public function getRecentApplications(int $days = 7): Collection
    {
        return JobApplication::recent($days)->orderBy('applied_at', 'desc')->get();
    }

    /**
     * Valide les données de la candidature.
     *
     * @param array $data
     * @return void
     * @throws JobOfferException
     */
    private function validateApplicationData(array $data): void
    {
        // Check for duplicate application from same email for same job offer
        if (isset($data['job_offer_id'], $data['applicant_email'])) {
            $existingApplication = JobApplication::where('job_offer_id', $data['job_offer_id'])
                ->where('applicant_email', $data['applicant_email'])
                ->first();

            if ($existingApplication) {
                throw JobOfferException::duplicateApplication();
            }
        }
    }

    /**
     * Valide l'offre d'emploi pour la candidature.
     *
     * @param int $jobOfferId
     * @return void
     * @throws JobOfferException
     */
    private function validateJobOfferForApplication(int $jobOfferId): void
    {
        $jobOffer = JobOffer::find($jobOfferId);

        if (!$jobOffer) {
            throw JobOfferException::jobOfferNotFound();
        }

        if (!$jobOffer->is_active) {
            throw JobOfferException::jobOfferNotActive();
        }

        if ($jobOffer->isExpired()) {
            throw JobOfferException::jobOfferExpired();
        }
    }

    /**
     * Télécharge un fichier.
     *
     * @param UploadedFile $file
     * @param string $type
     * @param string $applicationNumber
     * @return array
     * @throws JobOfferException
     */
    private function uploadFile(UploadedFile $file, string $type, string $applicationNumber): array
    {
        Log::info('JobApplicationService: Téléchargement du fichier', [
            'type' => $type,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'application_number' => $applicationNumber
        ]);

        // Validate file
        $this->validateUploadedFile($file);

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = $applicationNumber . '_' . $type . '_' . Str::random(8) . '.' . $extension;

        // Store file
        $path = $file->storeAs(self::UPLOAD_DIRECTORY, $filename);

        if (!$path) {
            throw JobOfferException::fileUploadFailed();
        }

        Log::info('JobApplicationService: Fichier téléchargé avec succès', [
            'path' => $path,
            'type' => $type,
            'application_number' => $applicationNumber
        ]);

        return [
            'path' => $path,
            'original_name' => $file->getClientOriginalName()
        ];
    }

    /**
     * Valide un fichier téléchargé.
     *
     * @param UploadedFile $file
     * @return void
     * @throws JobOfferException
     */
    private function validateUploadedFile(UploadedFile $file): void
    {
        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw JobOfferException::fileTooLarge();
        }

        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_CV_EXTENSIONS)) {
            throw JobOfferException::invalidFileType();
        }

        // Check if file is valid
        if (!$file->isValid()) {
            throw JobOfferException::invalidFile();
        }
    }

    /**
     * Génère un numéro de candidature unique.
     *
     * @return string
     */
    private function generateApplicationNumber(): string
    {
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $attempt++;

            // Format: APP-YYYY-MMDD-XXXX (APP = Application, YYYY = year, MMDD = month+day, XXXX = random)
            $year = date('Y');
            $monthDay = date('md');
            $randomNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $applicationNumber = "APP-{$year}-{$monthDay}-{$randomNumber}";

            // Check if this number already exists
            $exists = JobApplication::where('application_number', $applicationNumber)->exists();

            if (!$exists) {
                Log::info('JobApplicationService: Numéro de candidature unique généré', [
                    'application_number' => $applicationNumber,
                    'attempts' => $attempt
                ]);
                return $applicationNumber;
            }

            Log::debug('JobApplicationService: Le numéro de candidature généré existe déjà, nouvelle tentative', [
                'application_number' => $applicationNumber,
                'attempt' => $attempt
            ]);

        } while ($attempt < $maxAttempts);

        // If we couldn't generate a unique number after max attempts, use timestamp-based approach
        $timestamp = time();
        $applicationNumber = "APP-{$year}-{$monthDay}-" . substr($timestamp, -4);

        Log::warning('JobApplicationService: Utilisation d\'un numéro de candidature basé sur l\'horodatage après le nombre maximum de tentatives', [
            'application_number' => $applicationNumber,
            'max_attempts_reached' => $maxAttempts
        ]);

        return $applicationNumber;
    }

    /**
     * Récupère les statistiques des candidatures.
     *
     * @return array
     */
    public function getApplicationStatistics(): array
    {
        return [
            'total_applications' => JobApplication::count(),
            'pending_applications' => JobApplication::pending()->count(),
            'reviewed_applications' => JobApplication::reviewed()->count(),
            'accepted_applications' => JobApplication::accepted()->count(),
            'rejected_applications' => JobApplication::rejected()->count(),
            'applications_by_status' => JobApplication::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get()
                ->pluck('total', 'status')
                ->toArray(),
            'recent_applications_7_days' => JobApplication::recent(7)->count(),
            'recent_applications_30_days' => JobApplication::recent(30)->count(),
        ];
    }
}