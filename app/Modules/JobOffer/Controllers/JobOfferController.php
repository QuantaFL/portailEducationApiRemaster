<?php

namespace App\Modules\JobOffer\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\JobOffer\Exceptions\JobOfferException;
use App\Modules\JobOffer\Models\JobOffer;
use App\Modules\JobOffer\Requests\JobOfferRequest;
use App\Modules\JobOffer\Resources\JobOfferResource;
use App\Modules\JobOffer\Services\JobOfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class JobOfferController
 *
 * Gère les requêtes liées aux offres d'emploi.
 */
class JobOfferController extends Controller
{
    private JobOfferService $jobOfferService;

    public function __construct(JobOfferService $jobOfferService)
    {
        $this->jobOfferService = $jobOfferService;
    }

    /**
     * Affiche une liste des offres d'emploi.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            Log::info('JobOfferController: Récupération de la liste des offres d\'emploi', [
                'filters' => $request->all()
            ]);

            $filters = $request->only([
                'subject_id', 'employment_type', 'experience_level', 'location',
                'is_active', 'not_expired', 'search', 'order_by', 'order_direction', 'per_page'
            ]);

            $jobOffers = $this->jobOfferService->getAllJobOffers($filters);

            return response()->json([
                'success' => true,
                'message' => 'Offres d\'emploi récupérées avec succès',
                'data' => JobOfferResource::collection($jobOffers->items()),
                'meta' => [
                    'current_page' => $jobOffers->currentPage(),
                    'total' => $jobOffers->total(),
                    'per_page' => $jobOffers->perPage(),
                    'last_page' => $jobOffers->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Erreur lors de la récupération des offres d\'emploi', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la récupération des offres d\'emploi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche une offre d'emploi spécifique.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            Log::info('JobOfferController: Récupération de l\'offre d\'emploi', ['id' => $id]);

            $jobOffer = $this->jobOfferService->getJobOfferById($id);

            return response()->json([
                'success' => true,
                'message' => 'Offre d\'emploi récupérée avec succès',
                'data' => new JobOfferResource($jobOffer)
            ]);
        } catch (JobOfferException $e) {
            Log::warning('JobOfferController: Offre d\'emploi non trouvée', ['id' => $id]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Erreur lors de la récupération de l\'offre d\'emploi', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la récupération de l\'offre d\'emploi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enregistre une nouvelle offre d'emploi.
     *
     * @param JobOfferRequest $request
     * @return JsonResponse
     */
    public function store(JobOfferRequest $request): JsonResponse
    {
        try {
            Log::info('JobOfferController: Création d\'une offre d\'emploi', [
                'title' => $request->get('title'),
                'subject_id' => $request->get('subject_id')
            ]);

            $data = $request->validated();
            $data['posted_by'] = Auth::id();

            $jobOffer = $this->jobOfferService->createJobOffer($data);

            return response()->json([
                'success' => true,
                'message' => 'Offre d\'emploi créée avec succès',
                'data' => new JobOfferResource($jobOffer)
            ], 201);
        } catch (JobOfferException $e) {
            Log::warning('JobOfferController: Erreur de validation lors de la création de l\'offre d\'emploi', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Erreur lors de la création de l\'offre d\'emploi', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la création de l\'offre d\'emploi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour une offre d'emploi spécifique.
     *
     * @param JobOfferRequest $request
     * @param JobOffer $jobOffer
     * @return JsonResponse
     */
    public function update(JobOfferRequest $request, JobOffer $jobOffer): JsonResponse
    {
        try {
            Log::info('JobOfferController: Mise à jour de l\'offre d\'emploi', [
                'job_offer_id' => $jobOffer->id,
                'offer_number' => $jobOffer->offer_number
            ]);

            $data = $request->validated();
            $updatedJobOffer = $this->jobOfferService->updateJobOffer($jobOffer, $data);

            return response()->json([
                'success' => true,
                'message' => 'Offre d\'emploi mise à jour avec succès',
                'data' => new JobOfferResource($updatedJobOffer)
            ]);
        } catch (JobOfferException $e) {
            Log::warning('JobOfferController: Erreur de validation lors de la mise à jour de l\'offre d\'emploi', [
                'error' => $e->getMessage(),
                'job_offer_id' => $jobOffer->id
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Erreur lors de la mise à jour de l\'offre d\'emploi', [
                'error' => $e->getMessage(),
                'job_offer_id' => $jobOffer->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la mise à jour de l\'offre d\'emploi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprime une offre d'emploi.
     *
     * @param JobOffer $jobOffer
     * @return JsonResponse
     */
    public function destroy(JobOffer $jobOffer): JsonResponse
    {
        try {
            Log::info('JobOfferController: Suppression de l\'offre d\'emploi', [
                'job_offer_id' => $jobOffer->id,
                'offer_number' => $jobOffer->offer_number
            ]);

            $this->jobOfferService->deleteJobOffer($jobOffer);

            return response()->json([
                'success' => true,
                'message' => 'Offre d\'emploi supprimée avec succès'
            ]);
        } catch (JobOfferException $e) {
            Log::warning('JobOfferController: Impossible de supprimer l\'offre d\'emploi', [
                'error' => $e->getMessage(),
                'job_offer_id' => $jobOffer->id
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Erreur lors de la suppression de l\'offre d\'emploi', [
                'error' => $e->getMessage(),
                'job_offer_id' => $jobOffer->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la suppression de l\'offre d\'emploi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Publie une offre d'emploi.
     *
     * @param JobOffer $jobOffer
     * @return JsonResponse
     */
    public function publish(JobOffer $jobOffer): JsonResponse
    {
        try {
            Log::info('JobOfferController: Publication de l\'offre d\'emploi', [
                'job_offer_id' => $jobOffer->id,
                'offer_number' => $jobOffer->offer_number
            ]);

            $publishedJobOffer = $this->jobOfferService->publishJobOffer($jobOffer);

            return response()->json([
                'success' => true,
                'message' => 'Offre d\'emploi publiée avec succès',
                'data' => new JobOfferResource($publishedJobOffer)
            ]);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Erreur lors de la publication de l\'offre d\'emploi', [
                'error' => $e->getMessage(),
                'job_offer_id' => $jobOffer->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la publication de l\'offre d\'emploi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dépublie une offre d'emploi.
     *
     * @param JobOffer $jobOffer
     * @return JsonResponse
     */
    public function unpublish(JobOffer $jobOffer): JsonResponse
    {
        try {
            Log::info('JobOfferController: Dépublication de l\'offre d\'emploi', [
                'job_offer_id' => $jobOffer->id,
                'offer_number' => $jobOffer->offer_number
            ]);

            $unpublishedJobOffer = $this->jobOfferService->unpublishJobOffer($jobOffer);

            return response()->json([
                'success' => true,
                'message' => 'Offre d\'emploi dépubliée avec succès',
                'data' => new JobOfferResource($unpublishedJobOffer)
            ]);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Erreur lors de la dépublication de l\'offre d\'emploi', [
                'error' => $e->getMessage(),
                'job_offer_id' => $jobOffer->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la dépublication de l\'offre d\'emploi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les offres d'emploi actives.
     *
     * @return JsonResponse
     */
    public function active(): JsonResponse
    {
        try {
            Log::info('JobOfferController: Récupération des offres d\'emploi actives');

            $jobOffers = $this->jobOfferService->getActiveJobOffers();

            return response()->json([
                'success' => true,
                'message' => 'Offres d\'emploi actives récupérées avec succès',
                'data' => JobOfferResource::collection($jobOffers)
            ]);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Erreur lors de la récupération des offres d\'emploi actives', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la récupération des offres d\'emploi actives',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les offres d'emploi par matière.
     *
     * @param int $subjectId
     * @return JsonResponse
     */
    public function bySubject(int $subjectId): JsonResponse
    {
        try {
            Log::info('JobOfferController: Récupération des offres d\'emploi par matière', [
                'subject_id' => $subjectId
            ]);

            $jobOffers = $this->jobOfferService->getJobOffersBySubject($subjectId);

            return response()->json([
                'success' => true,
                'message' => 'Offres d\'emploi par matière récupérées avec succès',
                'data' => JobOfferResource::collection($jobOffers)
            ]);
        } catch (JobOfferException $e) {
            Log::warning('JobOfferController: Matière non trouvée', [
                'subject_id' => $subjectId
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Erreur lors de la récupération des offres d\'emploi par matière', [
                'error' => $e->getMessage(),
                'subject_id' => $subjectId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la récupération des offres d\'emploi par matière',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les statistiques des offres d'emploi.
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        try {
            Log::info('JobOfferController: Récupération des statistiques des offres d\'emploi');

            $statistics = $this->jobOfferService->getJobOfferStatistics();

            return response()->json([
                'success' => true,
                'message' => 'Statistiques des offres d\'emploi récupérées avec succès',
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Erreur lors de la récupération des statistiques des offres d\'emploi', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la récupération des statistiques des offres d\'emploi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère une offre d'emploi par son numéro.
     *
     * @param string $offerNumber
     * @return JsonResponse
     */
    public function byNumber(string $offerNumber): JsonResponse
    {
        try {
            Log::info('JobOfferController: Récupération de l\'offre d\'emploi par numéro', [
                'offer_number' => $offerNumber
            ]);

            $jobOffer = $this->jobOfferService->getJobOfferByNumber($offerNumber);

            return response()->json([
                'success' => true,
                'message' => 'Offre d\'emploi récupérée avec succès',
                'data' => new JobOfferResource($jobOffer)
            ]);
        } catch (JobOfferException $e) {
            Log::warning('JobOfferController: Offre d\'emploi non trouvée par numéro', [
                'offer_number' => $offerNumber
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Erreur lors de la récupération de l\'offre d\'emploi par numéro', [
                'error' => $e->getMessage(),
                'offer_number' => $offerNumber
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec de la récupération de l\'offre d\'emploi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}