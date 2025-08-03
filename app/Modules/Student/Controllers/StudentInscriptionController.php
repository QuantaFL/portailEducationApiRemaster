<?php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Student\Exceptions\StudentInscriptionException;
use App\Modules\Student\Requests\StudentInscriptionRequest;
use App\Modules\Student\Resources\StudentResource;
use App\Modules\Student\Resources\StudentSessionResource;
use App\Modules\Student\Services\StudentInscriptionService;

class StudentInscriptionController extends Controller
{
    private StudentInscriptionService $inscriptionService;

    public function __construct(StudentInscriptionService $inscriptionService)
    {
        $this->inscriptionService = $inscriptionService;
    }

    public function store(StudentInscriptionRequest $request)
    {
        try {
            $academicRecordsFile = $request->file('academic_records');
            $photoFile = $request->file('photo');

            $result = $this->inscriptionService->processInscription(
                $request->validated(),
                $academicRecordsFile,
                $photoFile
            );

            return response()->json([
                'student' => new StudentResource($result['student']),
                'student_session' => new StudentSessionResource($result['student_session']),
                'academic_records_url' => $result['student']->academic_records_url,
                'photo_url' => $result['student']->photo_url,
              //  'parent_password' => $result['parent_password'],
              //  'student_password' => $result['student_password'],
            ], 201);

        } catch (StudentInscriptionException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
