<?php

use App\Modules\Assignement\Controllers\AssignementController;
use App\Modules\ClassModel\Controllers\ClassModelController;
use App\Modules\Grade\Controllers\GradeController;
use App\Modules\Parent\Controllers\ParentController;
use App\Modules\ReportCard\Controllers\ReportCardController;
use App\Modules\AcademicYear\Controllers\AcademicYearController;
use App\Modules\Student\Controllers\StudentController;
use App\Modules\Subject\Controllers\SubjectController;
use App\Modules\Teacher\Controllers\TeacherController;
use App\Modules\Term\Controllers\TermController;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');
Route::prefix('v1')->group(function () {
    Route::apiResource('assignements', AssignementController::class);
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('students', StudentController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('academic-years', AcademicYearController::class);
    Route::apiResource('terms', TermController::class);
    Route::apiResource('grades', GradeController::class);
    Route::apiResource('parents', ParentController::class);
    Route::apiResource('classes', ClassModelController::class);
    Route::apiResource('report-cards', ReportCardController::class);
});
