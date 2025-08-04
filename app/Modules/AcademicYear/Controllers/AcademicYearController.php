<?php

namespace App\Modules\AcademicYear\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Exceptions\AcademicYearException;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Requests\AcademicYearRequest;
use App\Modules\AcademicYear\Ressources\AcademicYearResource;
use App\Modules\AcademicYear\Services\AcademicYearService;
use App\Modules\Term\Ressources\TermResource;
use Illuminate\Http\JsonResponse;

/**
 * Class AcademicYearController
 *
 * Gère les requêtes liées aux années académiques.
 */
class AcademicYearController extends Controller
{
    /**
     * @var AcademicYearService
     */
    private AcademicYearService $academicYearService;

    /**
     * AcademicYearController constructor.
     *
     * @param AcademicYearService $academicYearService
     */
    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    /**
     * Affiche une liste des années académiques.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $academicYears = $this->academicYearService->getAllAcademicYears();
        return response()->json(AcademicYearResource::collection($academicYears));
    }

    /**
     * Enregistre une nouvelle année académique.
     *
     * @param AcademicYearRequest $request
     * @return JsonResponse
     */
    public function store(AcademicYearRequest $request): JsonResponse
    {
        try {
            $result = $this->academicYearService->createAcademicYear($request->validated());

            return response()->json([
                'academic_year' => new AcademicYearResource($result['academic_year']),
                'terms' => $result['terms']
            ]);
        } catch (AcademicYearException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * Affiche une année académique spécifique.
     *
     * @param AcademicYear $academicYear
     * @return JsonResponse
     */
    public function show(AcademicYear $academicYear): JsonResponse
    {
        return response()->json(new AcademicYearResource($academicYear));
    }

    /**
     * Met à jour une année académique spécifique.
     *
     * @param AcademicYearRequest $request
     * @param AcademicYear $academicYear
     * @return JsonResponse
     */
    public function update(AcademicYearRequest $request, AcademicYear $academicYear): JsonResponse
    {
        try {
            $updatedAcademicYear = $this->academicYearService->updateAcademicYear($academicYear, $request->validated());
            return response()->json(new AcademicYearResource($updatedAcademicYear));
        } catch (AcademicYearException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * Supprime une année académique spécifique.
     *
     * @param AcademicYear $academicYear
     * @return JsonResponse
     */
    public function destroy(AcademicYear $academicYear): JsonResponse
    {
        try {
            $this->academicYearService->deleteAcademicYear($academicYear);
            return response()->json();
        } catch (AcademicYearException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * Récupère l'année académique en cours.
     *
     * @return JsonResponse
     */
    public function getCurrentAcademicYear(): JsonResponse
    {
        try {
            $currentYear = $this->academicYearService->getCurrentAcademicYear();
            return response()->json(new AcademicYearResource($currentYear));
        } catch (AcademicYearException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * Récupère les années académiques actives.
     *
     * @return JsonResponse
     */
    public function getActiveAcademicYears(): JsonResponse
    {
        try {
            $activeYears = $this->academicYearService->getActiveAcademicYears();
            return response()->json(AcademicYearResource::collection($activeYears));
        } catch (AcademicYearException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * Récupère une année académique par son ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getAcademicYearById(int $id): JsonResponse
    {
        try {
            $academicYear = $this->academicYearService->getAcademicYearById($id);
            return response()->json(new AcademicYearResource($academicYear));
        } catch (AcademicYearException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * Récupère les semestres d'une année académique donnée.
     *
     * @param int $academicYearId
     * @return JsonResponse
     */
    public function getTermsByAcademicYear(int $academicYearId): JsonResponse
    {
        try {
            $terms = $this->academicYearService->getTermsByAcademicYear($academicYearId);
            return response()->json(TermResource::collection($terms));
        } catch (AcademicYearException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
