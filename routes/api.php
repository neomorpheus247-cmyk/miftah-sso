<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout/schedule', [AuthController::class, 'scheduleLogout']);
    Route::post('/logout/cancel', [AuthController::class, 'cancelScheduledLogout']);

    // Course routes
    Route::apiResource('courses', CourseController::class);
    Route::post('courses/{course}/enroll', [CourseController::class, 'enroll']);
    Route::post('courses/{course}/unenroll', [CourseController::class, 'unenroll']);
});