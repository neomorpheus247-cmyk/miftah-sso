<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;

Route::get('/', function () {
    return view('welcome');
});

// Google SSO Routes
Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle'])
    ->name('google.login');
Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])
    ->name('google.callback');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Common Routes
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Admin Routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
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
