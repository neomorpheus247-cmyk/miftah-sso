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
| These routes are typically stateless, but since you are using Sanctum
| with session-based authentication, you should protect them with
| `auth:sanctum` instead of `auth:api`.
|--------------------------------------------------------------------------
*/

// User routes
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('roles');
});

// Auth routes (Sanctum session)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout/schedule', [AuthController::class, 'scheduleLogout']);
    Route::post('/logout/cancel', [AuthController::class, 'cancelScheduledLogout']);
});

// Course routes
Route::middleware('auth:sanctum')->apiResource('courses', CourseController::class);

// Course enrollment routes
Route::prefix('courses')->group(function () {
    Route::post('{courseId}/enroll', [CourseEnrollmentController::class, 'enroll'])
        ->middleware('auth:sanctum');
    Route::delete('{courseId}/enroll', [CourseEnrollmentController::class, 'unenroll'])
        ->middleware('auth:sanctum');
    Route::get('{courseId}/students', [CourseEnrollmentController::class, 'getEnrolledStudents'])
        ->middleware('auth:sanctum');
});
