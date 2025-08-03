<?php

use App\Modules\Assignement\Controllers\AssignementController;
use App\Modules\ClassModel\Controllers\ClassModelController;
use App\Modules\Grade\Controllers\GradeController;
use App\Modules\Parent\Controllers\ParentController;
use App\Modules\ReportCard\Controllers\ReportCardController;
use App\Modules\AcademicYear\Controllers\AcademicYearController;
use App\Modules\Student\Controllers\StudentController;
use App\Modules\Student\Controllers\StudentInscriptionController;
use App\Modules\Subject\Controllers\SubjectController;
use App\Modules\Teacher\Controllers\TeacherController;
use App\Modules\Term\Controllers\TermController;
use App\Modules\User\Controllers\AuthController;
use \App\Modules\Grade\Controllers\GradeClassController;
use App\Modules\Grade\Controllers\GradeStudentNotesController;

use Illuminate\Support\Facades\Route;



Route::prefix('v1')->group(function () {
    Route::get('grades/class/{classId}/teacher/{teacherId}/subject/{subjectId}/assignement/{assignementId?}/student/{studentId?}', [GradeClassController::class, 'getClassGrades']);
    Route::get('academic-year/current', [AcademicYearController::class, 'getCurrentAcademicYear']);
    Route::get('terms/current', [TermController::class, 'getCurrentTerm']);
    Route::get('academic-years/{academicYear}/terms', [AcademicYearController::class, 'getTermsByAcademicYear']);
    Route::get('academic-years/{academicYear}', [AcademicYearController::class, 'getAcademicYearById']);
    Route::apiResource('assignements', AssignementController::class);
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('students', StudentController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('academic-years', AcademicYearController::class);
    Route::apiResource('terms', TermController::class);
    Route::get('grades/class/{classId}/students/{studentId?}/teacher/{teacherId?}/subject/{subjectId?}/assignement/{assignementId?}', [GradeController::class, 'getStudentGradesInClassForTerm']);
    Route::apiResource('parents', ParentController::class);
    Route::get('parents/{parent}/children', [ParentController::class, 'children']);
    Route::apiResource('parents', ParentController::class);
    Route::get('users/{id}/parents', [ParentController::class, 'getParentByUserId']);
    Route::apiResource('classes', ClassModelController::class);
    Route::get('users/{id}/students', [StudentController::class, 'getStudentsByUserId']);
    Route::get('students/{id}/details', [StudentController::class, 'getStudentDetails']);
    Route::get('classes/{classId}/students', [ClassModelController::class, 'getStudentsByClass']);
    Route::apiResource('report-cards', ReportCardController::class);
    Route::post('report-cards/generate', [ReportCardController::class, 'generateReportCards']);
    Route::get('/grades', [GradeController::class, 'getGradesByTerm']);
    Route::post('/grades', [GradeController::class, 'updateGrades']);
    Route::post('grades/submit-term-notes/{class_id}', [GradeController::class, 'submitTermNotes']);
    Route::post('classes/{class_id}/notes/submit', [GradeController::class, 'submitTermNotes']);
    Route::get('teachers/{teacher}/classes', [TeacherController::class, 'getClasses']);
    Route::get('assignements/by-term-and-class', [AssignementController::class, 'getByTermAndClass']);
    Route::post('students/inscription', [StudentInscriptionController::class, 'store']);
    Route::get('teachers/{teacher}/subjects', [TeacherController::class, 'getTeacherSubjects']);
    Route::post('students/bulk', [StudentController::class, 'bulk']);
    Route::get('assignments/teacher/{id}', [AssignementController::class, 'getAssignmentsForTeacher']);
    Route::get('classes/{classId}/grades-matrix', [GradeController::class, 'getGradesMatrix']);
    Route::get('classes/{classId}/subjects/{subjectId}/assignments/{assignmentId}/teachers/{teacherId}/student-notes', [GradeStudentNotesController::class, 'getStudentNotes']);
    Route::post('teachers/dashboard/performance-summary/bulk', [TeacherController::class, 'getMultiClassPerformanceSummary']);
    Route::post('subjects/bulk', [SubjectController::class, 'getSubjectsByIds']);


    // Route::post('/send-teacher-contract', [TeacherContractController::class, 'sendContract']);

    // Auth routes
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->post('auth/change-password', [AuthController::class, 'changePassword']);
    Route::get('teacher/profile', [TeacherController::class, 'getTeacherProfile']);
    Route::get('teachers/users/{id}', [TeacherController::class, 'getTeacherByUserId']);
    Route::get('/students/{id}/next-classes', [
        StudentController::class,
        'getNextClasses',
    ]);
    Route::get('/report-cards/{id}/download', [
        App\Modules\ReportCard\Controllers\ReportCardController::class,
        'download',
    ]);
    Route::get('students/{studentId}/bulletins/latest', [
        App\Modules\ReportCard\Controllers\ReportCardController::class,
        'latestBulletinForStudent',
    ]);
    Route::get('students/{studentId}/report-cards', [ReportCardController::class, 'getReportCardsByStudent']);
});
