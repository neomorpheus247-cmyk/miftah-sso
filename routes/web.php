<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\SocialiteController;

// API Authentication Routes
Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle'])
    ->name('google.login');


Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])
    ->name('google.callback');

// Show role selection after Google login
Route::get('register/choose-role', [SocialiteController::class, 'showRoleSelection'])
    ->name('register.choose_role');
// Handle role selection
Route::post('register/choose-role', [SocialiteController::class, 'registerRole'])
    ->name('register.role');

Route::post('auth/logout', [SocialiteController::class, 'logout'])
    ->name('auth.logout')
    ->middleware('auth:sanctum');

// 🔑 Current user endpoint
Route::middleware('auth:sanctum')->get('/api/user', function (Request $request) {
    return $request->user()->load('roles');
});

// Protected API Routes
Route::middleware(['auth:sanctum'])->prefix('api')->group(function () {
    Route::middleware(['role:teacher|admin'])->group(function () {
        // Teacher-specific API endpoints
    });

    Route::middleware(['role:student|teacher|admin'])->group(function () {
        // Student-specific API endpoints
    });
});

// SPA Routes - Catch all routes and serve the Vue app
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api).*$');
