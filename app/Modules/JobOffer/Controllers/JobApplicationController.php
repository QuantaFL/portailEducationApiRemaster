<?php

namespace App\Modules\JobOffer\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\JobOffer\Exceptions\JobOfferException;
use App\Modules\JobOffer\Models\JobApplication;
use App\Modules\JobOffer\Requests\JobApplicationRequest;
use App\Modules\JobOffer\Requests\UpdateApplicationStatusRequest;
use App\Modules\JobOffer\Resources\JobApplicationResource;
use App\Modules\JobOffer\Services\JobApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class JobApplicationController
 *
 * Gère les requêtes liées aux candidatures.
 */
class JobApplicationController extends Controller
{
    private JobApplicationService $jobApplicationService;

    public function __construct(JobApplicationService $jobApplicationService)
    {
        $this->jobApplicationService = $jobApplicationService;
    }

    /**
     * Affiche une liste des candidatures.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            Log::info('JobApplicationController: Récupération de la liste des candidatures', [
                'filters' => $request->all()
            ]);

            $filters = $request->only([
                'job_offer_id', 'status', 'applicant_email', 'search', 'recent_days',
                'order_by', 'order_direction', 'per_page'
            ]);

            $applications = $this->jobApplicationService->getAllApplications($filters);

            return response()->json([
                'success' => true,
                'message' => 'Candidatures récupérées avec succès',
                'data' => JobApplicationResource::collection($applications->items()),
                'meta' => [
                    'current_page' => $applications->currentPage(),
                    'total' => $applications->total(),
                    'per_page' => $applications->perPage(),
                    'last_page' => $applications->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Erreur lors de la récupération des candidatures', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la récupération des candidatures',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche une candidature spécifique.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            Log::info('JobApplicationController: Récupération de la candidature', ['id' => $id]);

            $application = $this->jobApplicationService->getApplicationById($id);

            return response()->json([
                'success' => true,
                'message' => 'Candidature récupérée avec succès',
                'data' => new JobApplicationResource($application)
            ]);
        } catch (JobOfferException $e) {
            Log::warning('JobApplicationController: Candidature non trouvée', ['id' => $id]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Erreur lors de la récupération de la candidature', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la récupération de la candidature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enregistre une nouvelle candidature.
     *
     * @param JobApplicationRequest $request
     * @return JsonResponse
     */
    public function store(JobApplicationRequest $request): JsonResponse
    {
        try {
            Log::info('JobApplicationController: Création d\'une candidature', [
                'job_offer_id' => $request->get('job_offer_id'),
                'applicant_email' => $request->get('applicant_email'),
                'has_cv' => $request->hasFile('cv_file'),
                'has_cover_letter' => $request->hasFile('cover_letter_file')
            ]);

            $data = $request->validated();
            $cvFile = $request->file('cv_file');
            $coverLetterFile = $request->file('cover_letter_file');

            $application = $this->jobApplicationService->createApplication($data, $cvFile, $coverLetterFile);

            return response()->json([
                'success' => true,
                'message' => 'Candidature soumise avec succès',
                'data' => new JobApplicationResource($application)
            ], 201);
        } catch (JobOfferException $e) {
            Log::warning('JobApplicationController: Erreur de validation lors de la création de la candidature', [
                'error' => $e->getMessage(),
                'data' => $request->except(['cv_file', 'cover_letter_file'])
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Erreur lors de la création de la candidature', [
                'error' => $e->getMessage(),
                'data' => $request->except(['cv_file', 'cover_letter_file'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la soumission de la candidature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour le statut d'une candidature.
     *
     * @param UpdateApplicationStatusRequest $request
     * @param JobApplication $application
     * @return JsonResponse
     */
    public function updateStatus(UpdateApplicationStatusRequest $request, JobApplication $application): JsonResponse
    {
        try {
            Log::info('JobApplicationController: Mise à jour du statut de la candidature', [
                'application_id' => $application->id,
                'new_status' => $request->get('status')
            ]);

            $status = $request->get('status');
            $adminNotes = $request->get('admin_notes');
            $reviewedBy = Auth::id();

            $updatedApplication = $this->jobApplicationService->updateApplicationStatus(
                $application,
                $status,
                $reviewedBy,
                $adminNotes
            );

            return response()->json([
                'success' => true,
                'message' => 'Statut de la candidature mis à jour avec succès',
                'data' => new JobApplicationResource($updatedApplication)
            ]);
        } catch (JobOfferException $e) {
            Log::warning('JobApplicationController: Erreur de validation lors de la mise à jour du statut', [
                'error' => $e->getMessage(),
                'application_id' => $application->id
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Erreur lors de la mise à jour du statut de la candidature', [
                'error' => $e->getMessage(),
                'application_id' => $application->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la mise à jour du statut de la candidature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprime une candidature.
     *
     * @param JobApplication $application
     * @return JsonResponse
     */
    public function destroy(JobApplication $application): JsonResponse
    {
        try {
            Log::info('JobApplicationController: Suppression de la candidature', [
                'application_id' => $application->id,
                'application_number' => $application->application_number
            ]);

            $this->jobApplicationService->deleteApplication($application);

            return response()->json([
                'success' => true,
                'message' => 'Candidature supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Erreur lors de la suppression de la candidature', [
                'error' => $e->getMessage(),
                'application_id' => $application->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la suppression de la candidature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Télécharge un fichier d'une candidature.
     *
     * @param JobApplication $application
     * @param string $fileType
     * @return BinaryFileResponse|JsonResponse
     */
    public function downloadFile(JobApplication $application, string $fileType): BinaryFileResponse|JsonResponse
    {
        try {
            Log::info('JobApplicationController: Téléchargement du fichier', [
                'application_id' => $application->id,
                'file_type' => $fileType
            ]);

            $fileData = $this->jobApplicationService->downloadFile($application, $fileType);

            return response()->download(
                $fileData['path'],
                $fileData['original_name'],
                [
                    'Content-Type' => $fileData['mime_type'],
                    'Content-Length' => $fileData['size']
                ]
            );
        } catch (JobOfferException $e) {
            Log::warning('JobApplicationController: Fichier non trouvé', [
                'application_id' => $application->id,
                'file_type' => $fileType,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Erreur lors du téléchargement du fichier', [
                'error' => $e->getMessage(),
                'application_id' => $application->id,
                'file_type' => $fileType
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec du téléchargement du fichier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les candidatures par offre d'emploi.
     *
     * @param int $jobOfferId
     * @return JsonResponse
     */
    public function byJobOffer(int $jobOfferId): JsonResponse
    {
        try {
            Log::info('JobApplicationController: Récupération des candidatures par offre d\'emploi', [
                'job_offer_id' => $jobOfferId
            ]);

            $applications = $this->jobApplicationService->getApplicationsByJobOffer($jobOfferId);

            return response()->json([
                'success' => true,
                'message' => 'Candidatures récupérées avec succès',
                'data' => JobApplicationResource::collection($applications)
            ]);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Erreur lors de la récupération des candidatures par offre d\'emploi', [
                'error' => $e->getMessage(),
                'job_offer_id' => $jobOfferId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la récupération des candidatures',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les candidatures récentes.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function recent(Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 7);

            Log::info('JobApplicationController: Récupération des candidatures récentes', [
                'days' => $days
            ]);

            $applications = $this->jobApplicationService->getRecentApplications($days);

            return response()->json([
                'success' => true,
                'message' => 'Candidatures récentes récupérées avec succès',
                'data' => JobApplicationResource::collection($applications)
            ]);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Erreur lors de la récupération des candidatures récentes', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la récupération des candidatures récentes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les statistiques des candidatures.
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        try {
            Log::info('JobApplicationController: Récupération des statistiques des candidatures');

            $statistics = $this->jobApplicationService->getApplicationStatistics();

            return response()->json([
                'success' => true,
                'message' => 'Statistiques des candidatures récupérées avec succès',
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Erreur lors de la récupération des statistiques des candidatures', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la récupération des statistiques des candidatures',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère une candidature par son numéro.
     *
     * @param string $applicationNumber
     * @return JsonResponse
     */
    public function byNumber(string $applicationNumber): JsonResponse
    {
        try {
            Log::info('JobApplicationController: Récupération de la candidature par numéro', [
                'application_number' => $applicationNumber
            ]);

            $application = $this->jobApplicationService->getApplicationByNumber($applicationNumber);

            return response()->json([
                'success' => true,
                'message' => 'Candidature récupérée avec succès',
                'data' => new JobApplicationResource($application)
            ]);
        } catch (JobOfferException $e) {
            Log::warning('JobApplicationController: Candidature non trouvée par numéro', [
                'application_number' => $applicationNumber
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Erreur lors de la récupération de la candidature par numéro', [
                'error' => $e->getMessage(),
                'application_number' => $applicationNumber
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la récupération de la candidature',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
