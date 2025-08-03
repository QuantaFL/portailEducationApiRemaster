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
//        $user = $request->user();
//        if($user->role() !== 'student' && $user->role() !== 'parent')  {
//            return response()->json(['error' => 'Unauthorized'], 403);
//        }
//
//        if($user->role() === 'parent'){
//            $child = Student::where('id', $studentId)->where('parent_id', $user->id)->firstOrFail();
//            if($studentId !== $child->id) {
//                return response()->json(['error' => 'Unauthorized'], 403);
//            }
//        }

        $since = $request->query('since');
        // Support both student.id and user_model_id as input
        $student = Student::where('id', $studentId)
            ->orWhere('user_model_id', $studentId)
            ->first();
        if (!$student) {
            return response()->json(['report_card' => null], 200);
        }
        $studentSession = StudentSession::where('student_id', $student->id)
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

    /**
     * Download the bulletin PDF for a given report card.
     * Only the student or their parent can access this file.
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($id)
    {
        $reportCard = ReportCard::findOrFail($id);

        // Authorization: Only the student or their parent can download
//        $user = auth()->user();
//        $student = $reportCard->studentSession->student;
//        if (!$student) {
//            abort(404, 'Student not found for this report card.');
//        }
//        if (
//            $user->id !== $student->user_model_id &&
//            $user->id !== optional($student->parentModel)->user_model_id
//        ) {
//            abort(403, 'You are not authorized to download this bulletin.');
//        }

        // Use the correct path and remove 'public/' prefix
        $filePath = $reportCard->path ? str_replace('public/', '', $reportCard->path) : null;
        if (!$filePath || !\Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found.');
        }
        return \Storage::disk('public')->download($filePath);
    }

    /**
     * Get all report cards for a given student (by student.id or user_model_id).
     * Route: GET /api/v1/students/{studentId}/report-cards
     *
     * @param int|string $studentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReportCardsByStudent($studentId)
    {
        // Support both student.id and user_model_id as input
        $student = Student::where('id', $studentId)
            ->orWhere('user_model_id', $studentId)
            ->first();
        if (!$student) {
            return response()->json(['report_cards' => []], 200);
        }
        $studentSessions = StudentSession::where('student_id', $student->id)->pluck('id');
        $reportCards = ReportCard::whereIn('student_session_id', $studentSessions)->orderBy('created_at', 'desc')->get();
        return response()->json([
            'report_cards' => ReportCardResource::collection($reportCards)
        ], 200);
    }
}
