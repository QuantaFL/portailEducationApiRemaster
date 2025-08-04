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

class JobApplicationController extends Controller
{
    private JobApplicationService $jobApplicationService;

    public function __construct(JobApplicationService $jobApplicationService)
    {
        $this->jobApplicationService = $jobApplicationService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            Log::info('JobApplicationController: Getting applications list', [
                'filters' => $request->all()
            ]);
            
            $filters = $request->only([
                'job_offer_id', 'status', 'applicant_email', 'search', 'recent_days',
                'order_by', 'order_direction', 'per_page'
            ]);
            
            $applications = $this->jobApplicationService->getAllApplications($filters);
            
            return response()->json([
                'success' => true,
                'message' => 'Job applications retrieved successfully',
                'data' => JobApplicationResource::collection($applications->items()),
                'meta' => [
                    'current_page' => $applications->currentPage(),
                    'total' => $applications->total(),
                    'per_page' => $applications->perPage(),
                    'last_page' => $applications->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Error getting applications', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job applications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            Log::info('JobApplicationController: Getting job application', ['id' => $id]);
            
            $application = $this->jobApplicationService->getApplicationById($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Job application retrieved successfully',
                'data' => new JobApplicationResource($application)
            ]);
        } catch (JobOfferException $e) {
            Log::warning('JobApplicationController: Application not found', ['id' => $id]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Error getting application', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job application',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(JobApplicationRequest $request): JsonResponse
    {
        try {
            Log::info('JobApplicationController: Creating job application', [
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
                'message' => 'Job application submitted successfully',
                'data' => new JobApplicationResource($application)
            ], 201);
        } catch (JobOfferException $e) {
            Log::warning('JobApplicationController: Validation error creating application', [
                'error' => $e->getMessage(),
                'data' => $request->except(['cv_file', 'cover_letter_file'])
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Error creating application', [
                'error' => $e->getMessage(),
                'data' => $request->except(['cv_file', 'cover_letter_file'])
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit job application',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(UpdateApplicationStatusRequest $request, JobApplication $application): JsonResponse
    {
        try {
            Log::info('JobApplicationController: Updating application status', [
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
                'message' => 'Application status updated successfully',
                'data' => new JobApplicationResource($updatedApplication)
            ]);
        } catch (JobOfferException $e) {
            Log::warning('JobApplicationController: Validation error updating status', [
                'error' => $e->getMessage(),
                'application_id' => $application->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Error updating application status', [
                'error' => $e->getMessage(),
                'application_id' => $application->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update application status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(JobApplication $application): JsonResponse
    {
        try {
            Log::info('JobApplicationController: Deleting job application', [
                'application_id' => $application->id,
                'application_number' => $application->application_number
            ]);
            
            $this->jobApplicationService->deleteApplication($application);
            
            return response()->json([
                'success' => true,
                'message' => 'Job application deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Error deleting application', [
                'error' => $e->getMessage(),
                'application_id' => $application->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete job application',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadFile(JobApplication $application, string $fileType): BinaryFileResponse|JsonResponse
    {
        try {
            Log::info('JobApplicationController: Downloading file', [
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
            Log::warning('JobApplicationController: File not found', [
                'application_id' => $application->id,
                'file_type' => $fileType,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Error downloading file', [
                'error' => $e->getMessage(),
                'application_id' => $application->id,
                'file_type' => $fileType
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to download file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function byJobOffer(int $jobOfferId): JsonResponse
    {
        try {
            Log::info('JobApplicationController: Getting applications by job offer', [
                'job_offer_id' => $jobOfferId
            ]);
            
            $applications = $this->jobApplicationService->getApplicationsByJobOffer($jobOfferId);
            
            return response()->json([
                'success' => true,
                'message' => 'Applications retrieved successfully',
                'data' => JobApplicationResource::collection($applications)
            ]);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Error getting applications by job offer', [
                'error' => $e->getMessage(),
                'job_offer_id' => $jobOfferId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve applications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function recent(Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 7);
            
            Log::info('JobApplicationController: Getting recent applications', [
                'days' => $days
            ]);
            
            $applications = $this->jobApplicationService->getRecentApplications($days);
            
            return response()->json([
                'success' => true,
                'message' => 'Recent applications retrieved successfully',
                'data' => JobApplicationResource::collection($applications)
            ]);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Error getting recent applications', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve recent applications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            Log::info('JobApplicationController: Getting application statistics');
            
            $statistics = $this->jobApplicationService->getApplicationStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'Application statistics retrieved successfully',
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Error getting application statistics', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve application statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function byNumber(string $applicationNumber): JsonResponse
    {
        try {
            Log::info('JobApplicationController: Getting application by number', [
                'application_number' => $applicationNumber
            ]);
            
            $application = $this->jobApplicationService->getApplicationByNumber($applicationNumber);
            
            return response()->json([
                'success' => true,
                'message' => 'Job application retrieved successfully',
                'data' => new JobApplicationResource($application)
            ]);
        } catch (JobOfferException $e) {
            Log::warning('JobApplicationController: Application not found by number', [
                'application_number' => $applicationNumber
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('JobApplicationController: Error getting application by number', [
                'error' => $e->getMessage(),
                'application_number' => $applicationNumber
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job application',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}