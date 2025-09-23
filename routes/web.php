<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;

// API Authentication Routes
Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle'])
    ->name('google.login');
Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])
    ->name('google.callback');
Route::post('auth/logout', [SocialiteController::class, 'logout'])
    ->name('auth.logout')
    ->middleware('auth');

// Protected API Routes
Route::middleware(['auth'])->prefix('api')->group(function () {
    // Your protected API routes here
});

// SPA Routes - Catch all routes and serve the Vue app
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api).*$');
    });

    // Teacher Routes
    Route::middleware(['role:teacher,admin'])->group(function () {
        Route::get('/teacher', function () {
            return view('teacher.dashboard');
        })->name('teacher.dashboard');
    });

    // Student Routes
    Route::middleware(['role:student,teacher,admin'])->group(function () {
        Route::get('/student', function () {
            return view('student.dashboard');
        })->name('student.dashboard');
    });
});
