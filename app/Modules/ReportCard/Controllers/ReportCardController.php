<?php

namespace App\Modules\ReportCard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ReportCard\Models\ReportCard;
use App\Modules\ReportCard\Requests\ReportCardRequest;
use App\Modules\ReportCard\Ressources\ReportCardResource;
use App\Modules\ReportCard\Requests\GenerateReportCardsRequest;
use App\Modules\ReportCard\Services\ReportCardGeneratorService;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportCardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(ReportCardResource::collection(ReportCard::all()));
    }

    public function store(ReportCardRequest $request): JsonResponse
    {
        return response()->json(new ReportCardResource(ReportCard::create($request->validated())));
    }

    public function show(ReportCard $reportCard): JsonResponse
    {
        return response()->json(new ReportCardResource($reportCard));
    }

    public function update(ReportCardRequest $request, ReportCard $reportCard)
    {
        $reportCard->update($request->validated());

        return response()->json(new ReportCardResource($reportCard));
    }

    public function destroy(ReportCard $reportCard): JsonResponse
    {
        $reportCard->delete();

        return response()->json();
    }

    public function generateReportCards(GenerateReportCardsRequest $request, ReportCardGeneratorService $reportCardGeneratorService): JsonResponse
    {
        try {
            $generatedReportCards = $reportCardGeneratorService->generateReportCardsForClassAndTerm(
                $request->class_model_id,
                $request->term_id
            );

            $reportCardIds = collect($generatedReportCards)->pluck('report_card_model.id')->toArray();
            $reloadedReportCards = ReportCard::whereIn('id', $reportCardIds)->get();

            return response()->json(ReportCardResource::collection($reloadedReportCards));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Get the latest report card (bulletin) for a student, optionally filtered by a 'since' timestamp.
     * Route: GET /v1/students/{studentId}/bulletins/latest
     * Query param: since (ISO8601 string, optional)
     *
     * @param Request $request
     * @param int $studentId
     * @return JsonResponse
     */
    public function latestBulletinForStudent(Request $request, $studentId): JsonResponse
    {
        $user = $request->user();
        if($user->role() !== 'student' && $user->role() !== 'parent')  {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if($user->role() === 'parent'){
            $child = Student::where('id', $studentId)->where('parent_id', $user->id)->firstOrFail();
            if($studentId !== $child->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $since = $request->query('since');
        $studentSession = StudentSession::where('student_id', $studentId)
            ->latest('id')
            ->first();
        if (!$studentSession) {
            return response()->json(['report_card' => null], 200);
        }
        $query = ReportCard::where('student_session_id', $studentSession->id);
        if ($since) {
            $sinceDate = date_create($since);
            if ($sinceDate) {
                $query->where('created_at', '>', $sinceDate);
            }
        }
        $reportCard = $query->latest('created_at')->first();
        if (!$reportCard) {
            return response()->json(['report_card' => null], 200);
        }
        return response()->json([
            'report_card' => new ReportCardResource($reportCard)
        ], 200);
    }
}
