<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CourseEnrollmentController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// User routes
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth routes
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout/schedule', [AuthController::class, 'scheduleLogout']);
    Route::post('/logout/cancel', [AuthController::class, 'cancelScheduledLogout']);
});

// Course routes
Route::apiResource('courses', CourseController::class);

// Course enrollment routes
Route::prefix('courses')->group(function () {
    Route::post('{courseId}/enroll', [CourseEnrollmentController::class, 'enroll']);
    Route::delete('{courseId}/enroll', [CourseEnrollmentController::class, 'unenroll']);
    Route::get('{courseId}/students', [CourseEnrollmentController::class, 'getEnrolledStudents']);
});