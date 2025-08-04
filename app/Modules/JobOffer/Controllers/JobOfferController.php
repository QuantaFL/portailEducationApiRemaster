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

class JobOfferController extends Controller
{
    private JobOfferService $jobOfferService;

    public function __construct(JobOfferService $jobOfferService)
    {
        $this->jobOfferService = $jobOfferService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            Log::info('JobOfferController: Getting job offers list', [
                'filters' => $request->all()
            ]);
            
            $filters = $request->only([
                'subject_id', 'employment_type', 'experience_level', 'location',
                'is_active', 'not_expired', 'search', 'order_by', 'order_direction', 'per_page'
            ]);
            
            $jobOffers = $this->jobOfferService->getAllJobOffers($filters);
            
            return response()->json([
                'success' => true,
                'message' => 'Job offers retrieved successfully',
                'data' => JobOfferResource::collection($jobOffers->items()),
                'meta' => [
                    'current_page' => $jobOffers->currentPage(),
                    'total' => $jobOffers->total(),
                    'per_page' => $jobOffers->perPage(),
                    'last_page' => $jobOffers->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Error getting job offers', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job offers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            Log::info('JobOfferController: Getting job offer', ['id' => $id]);
            
            $jobOffer = $this->jobOfferService->getJobOfferById($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Job offer retrieved successfully',
                'data' => new JobOfferResource($jobOffer)
            ]);
        } catch (JobOfferException $e) {
            Log::warning('JobOfferController: Job offer not found', ['id' => $id]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Error getting job offer', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job offer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(JobOfferRequest $request): JsonResponse
    {
        try {
            Log::info('JobOfferController: Creating job offer', [
                'title' => $request->get('title'),
                'subject_id' => $request->get('subject_id')
            ]);
            
            $data = $request->validated();
            $data['posted_by'] = Auth::id();
            
            $jobOffer = $this->jobOfferService->createJobOffer($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Job offer created successfully',
                'data' => new JobOfferResource($jobOffer)
            ], 201);
        } catch (JobOfferException $e) {
            Log::warning('JobOfferController: Validation error creating job offer', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Error creating job offer', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create job offer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(JobOfferRequest $request, JobOffer $jobOffer): JsonResponse
    {
        try {
            Log::info('JobOfferController: Updating job offer', [
                'job_offer_id' => $jobOffer->id,
                'offer_number' => $jobOffer->offer_number
            ]);
            
            $data = $request->validated();
            $updatedJobOffer = $this->jobOfferService->updateJobOffer($jobOffer, $data);
            
            return response()->json([
                'success' => true,
                'message' => 'Job offer updated successfully',
                'data' => new JobOfferResource($updatedJobOffer)
            ]);
        } catch (JobOfferException $e) {
            Log::warning('JobOfferController: Validation error updating job offer', [
                'error' => $e->getMessage(),
                'job_offer_id' => $jobOffer->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Error updating job offer', [
                'error' => $e->getMessage(),
                'job_offer_id' => $jobOffer->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update job offer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(JobOffer $jobOffer): JsonResponse
    {
        try {
            Log::info('JobOfferController: Deleting job offer', [
                'job_offer_id' => $jobOffer->id,
                'offer_number' => $jobOffer->offer_number
            ]);
            
            $this->jobOfferService->deleteJobOffer($jobOffer);
            
            return response()->json([
                'success' => true,
                'message' => 'Job offer deleted successfully'
            ]);
        } catch (JobOfferException $e) {
            Log::warning('JobOfferController: Cannot delete job offer', [
                'error' => $e->getMessage(),
                'job_offer_id' => $jobOffer->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Error deleting job offer', [
                'error' => $e->getMessage(),
                'job_offer_id' => $jobOffer->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete job offer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function publish(JobOffer $jobOffer): JsonResponse
    {
        try {
            Log::info('JobOfferController: Publishing job offer', [
                'job_offer_id' => $jobOffer->id,
                'offer_number' => $jobOffer->offer_number
            ]);
            
            $publishedJobOffer = $this->jobOfferService->publishJobOffer($jobOffer);
            
            return response()->json([
                'success' => true,
                'message' => 'Job offer published successfully',
                'data' => new JobOfferResource($publishedJobOffer)
            ]);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Error publishing job offer', [
                'error' => $e->getMessage(),
                'job_offer_id' => $jobOffer->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish job offer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function unpublish(JobOffer $jobOffer): JsonResponse
    {
        try {
            Log::info('JobOfferController: Unpublishing job offer', [
                'job_offer_id' => $jobOffer->id,
                'offer_number' => $jobOffer->offer_number
            ]);
            
            $unpublishedJobOffer = $this->jobOfferService->unpublishJobOffer($jobOffer);
            
            return response()->json([
                'success' => true,
                'message' => 'Job offer unpublished successfully',
                'data' => new JobOfferResource($unpublishedJobOffer)
            ]);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Error unpublishing job offer', [
                'error' => $e->getMessage(),
                'job_offer_id' => $jobOffer->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to unpublish job offer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function active(): JsonResponse
    {
        try {
            Log::info('JobOfferController: Getting active job offers');
            
            $jobOffers = $this->jobOfferService->getActiveJobOffers();
            
            return response()->json([
                'success' => true,
                'message' => 'Active job offers retrieved successfully',
                'data' => JobOfferResource::collection($jobOffers)
            ]);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Error getting active job offers', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve active job offers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bySubject(int $subjectId): JsonResponse
    {
        try {
            Log::info('JobOfferController: Getting job offers by subject', [
                'subject_id' => $subjectId
            ]);
            
            $jobOffers = $this->jobOfferService->getJobOffersBySubject($subjectId);
            
            return response()->json([
                'success' => true,
                'message' => 'Job offers by subject retrieved successfully',
                'data' => JobOfferResource::collection($jobOffers)
            ]);
        } catch (JobOfferException $e) {
            Log::warning('JobOfferController: Subject not found', [
                'subject_id' => $subjectId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Error getting job offers by subject', [
                'error' => $e->getMessage(),
                'subject_id' => $subjectId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job offers by subject',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            Log::info('JobOfferController: Getting job offer statistics');
            
            $statistics = $this->jobOfferService->getJobOfferStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'Job offer statistics retrieved successfully',
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Error getting job offer statistics', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job offer statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function byNumber(string $offerNumber): JsonResponse
    {
        try {
            Log::info('JobOfferController: Getting job offer by number', [
                'offer_number' => $offerNumber
            ]);
            
            $jobOffer = $this->jobOfferService->getJobOfferByNumber($offerNumber);
            
            return response()->json([
                'success' => true,
                'message' => 'Job offer retrieved successfully',
                'data' => new JobOfferResource($jobOffer)
            ]);
        } catch (JobOfferException $e) {
            Log::warning('JobOfferController: Job offer not found by number', [
                'offer_number' => $offerNumber
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('JobOfferController: Error getting job offer by number', [
                'error' => $e->getMessage(),
                'offer_number' => $offerNumber
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job offer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}