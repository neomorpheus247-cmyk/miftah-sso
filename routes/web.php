<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// The name() method is what allows you to reference this route as 'login'.
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Google Authentication Routes
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])
    ->name('google.login');

Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])
    ->name('google.callback');

// Show role selection after Google login
Route::get('register/choose-role', [GoogleController::class, 'showRoleSelection'])
    ->name('register.choose_role')->middleware('auth');

// Handle role selection
Route::post('register/choose-role', [GoogleController::class, 'registerRole'])
    ->name('register.role')->middleware('auth');

// Logout Route
Route::post('auth/logout', [GoogleController::class, 'logout'])
    ->name('auth.logout')
    ->middleware('auth:sanctum');

// API Authentication Routes - These should ideally be in api.php
Route::middleware('auth:sanctum')->prefix('api')->group(function () {
    // Current user endpoint
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles');
    });

    // Protected API Routes
    Route::middleware(['role:teacher|admin'])->group(function () {
        // Teacher-specific API endpoints
    });

    Route::middleware(['role:student|teacher|admin'])->group(function () {
        // Student-specific API endpoints
    });
});

// Debug route to check session and user after login
Route::get('/debug-session', function (Request $request) {
    return [
        'user' => Auth::user(),
        'session' => $request->session()->all(),
    ];
});

// SPA Routes - Catch all routes and serve the Vue app
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api|auth).*$');
