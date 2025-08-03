<?php

namespace App\Modules\Subject\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Requests\SubjectRequest;
use App\Modules\Subject\Requests\ToggleStatusRequest;
use App\Modules\Subject\Ressources\SubjectResource;
use App\Modules\Subject\Services\SubjectService;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
    protected SubjectService $subjectService;

    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService = $subjectService;
    }

    public function index()
    {
        try {
            $subjects = $this->subjectService->getAllSubjects();
            return response()->json(SubjectResource::collection($subjects));
        } catch (\Exception $e) {
            Log::error('SubjectController: Failed to get subjects', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to retrieve subjects'], 500);
        }
    }

    public function store(SubjectRequest $request)
    {
        try {
            $subject = $this->subjectService->createSubject($request->validated());
            return response()->json(new SubjectResource($subject));
        } catch (\Exception $e) {
            Log::error('SubjectController: Failed to create subject', ['error' => $e->getMessage()]);
            
            if (str_contains($e->getMessage(), 'existe déjà')) {
                return response()->json(['message' => $e->getMessage()], 409);
            }
            
            return response()->json(['message' => 'Failed to create subject'], 500);
        }
    }

    public function show(Subject $subject)
    {
        Log::info('SubjectController: Showing subject', ['subject_id' => $subject->id]);
        return response()->json(new SubjectResource($subject));
    }

    public function update(SubjectRequest $request, Subject $subject)
    {
        try {
            $updatedSubject = $this->subjectService->updateSubject($subject, $request->validated());
            return response()->json(new SubjectResource($updatedSubject));
        } catch (\Exception $e) {
            Log::error('SubjectController: Failed to update subject', ['error' => $e->getMessage(), 'subject_id' => $subject->id]);
            
            if (str_contains($e->getMessage(), 'existe déjà')) {
                return response()->json(['message' => $e->getMessage()], 409);
            }
            
            return response()->json(['message' => 'Failed to update subject'], 500);
        }
    }

    public function destroy(Subject $subject)
    {
        try {
            $this->subjectService->deleteSubject($subject);
            return response()->json();
        } catch (\Exception $e) {
            Log::error('SubjectController: Failed to delete subject', ['error' => $e->getMessage(), 'subject_id' => $subject->id]);
            return response()->json(['message' => 'Failed to delete subject'], 500);
        }
    }

    public function getSubjectsByIds()
    {
        try {
            $ids = request()->input('ids', []);
            $subjects = $this->subjectService->getSubjectsByIds($ids);
            return response()->json(SubjectResource::collection($subjects));
        } catch (\Exception $e) {
            Log::error('SubjectController: Failed to get subjects by IDs', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to retrieve subjects'], 500);
        }
    }

    public function toggleStatus(ToggleStatusRequest $request)
    {
        try {
            $validated = $request->validated();
            $subjectName = $validated['name'];
            
            Log::info('SubjectController: Toggle status request received', [
                'subject_name' => $subjectName,
                'user_agent' => request()->header('User-Agent'),
                'ip' => request()->ip()
            ]);
            
            $subject = $this->subjectService->toggleStatusByName($subjectName);
            
            Log::info('SubjectController: Toggle status completed successfully', [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'new_status' => $subject->status
            ]);
            
            return response()->json(new SubjectResource($subject));
        } catch (\Exception $e) {
            Log::error('SubjectController: Failed to toggle subject status', [
                'error' => $e->getMessage(),
                'subject_name' => $request->input('name'),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            if (str_contains($e->getMessage(), "n'existe pas")) {
                return response()->json(['message' => $e->getMessage()], 404);
            }
            
            return response()->json(['message' => 'Failed to toggle subject status'], 500);
        }
    }
}
