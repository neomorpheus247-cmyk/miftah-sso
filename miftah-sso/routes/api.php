<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

    // Course routes
    Route::apiResource('courses', CourseController::class);
    Route::post('courses/{course}/enroll', [CourseController::class, 'enroll']);
    Route::post('courses/{course}/unenroll', [CourseController::class, 'unenroll']);
});