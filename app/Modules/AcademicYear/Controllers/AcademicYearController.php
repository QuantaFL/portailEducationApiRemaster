<?php

namespace App\Modules\AcademicYear\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Exceptions\AcademicYearException;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Requests\AcademicYearRequest;
use App\Modules\AcademicYear\Ressources\AcademicYearResource;
use App\Modules\AcademicYear\Services\AcademicYearService;
use App\Modules\Term\Ressources\TermResource;

class AcademicYearController extends Controller
{
    private AcademicYearService $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    public function index()
    {
        $academicYears = $this->academicYearService->getAllAcademicYears();
        return response()->json(AcademicYearResource::collection($academicYears));
    }

    public function store(AcademicYearRequest $request)
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

    public function show(AcademicYear $academicYear)
    {
        return response()->json(new AcademicYearResource($academicYear));
    }

    public function update(AcademicYearRequest $request, AcademicYear $academicYear)
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

    public function destroy(AcademicYear $academicYear)
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

    public function getCurrentAcademicYear()
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

    public function getActiveAcademicYears()
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

    public function getAcademicYearById($id)
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

    public function getTermsByAcademicYear($academicYearId)
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
