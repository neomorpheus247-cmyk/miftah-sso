<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| These routes use the "web" middleware group and support Sanctum
| session-based authentication (cookies + CSRF).
|--------------------------------------------------------------------------
*/

// --- Authentication Views ---
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// --- Google Authentication ---
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])
    ->name('google.login');

Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])
    ->name('google.callback');

// --- Role Selection after Google login ---
Route::middleware('auth')->group(function () {
    Route::get('register/choose-role', [GoogleController::class, 'showRoleSelection'])
        ->name('register.choose_role');

    Route::post('register/choose-role', [GoogleController::class, 'registerRole'])
        ->name('register.role');

    // Logout (session-based)
    Route::post('auth/logout', [GoogleController::class, 'logout'])
        ->name('auth.logout');
});

// --- API (Session Auth with Sanctum) ---
// These use session cookies + CSRF (first-party SPA)
Route::prefix('api')->middleware(['web', 'auth'])->group(function () {
    // Current user endpoint
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles');
    });

    // Teacher/Admin-only APIs
    Route::middleware(['role:teacher|admin'])->group(function () {
        // Teacher-specific API endpoints here
    });

    // Student/Teacher/Admin APIs
    Route::middleware(['role:student|teacher|admin'])->group(function () {
        // Student-specific API endpoints here
    });
});

// --- Debug Route (for testing session + auth) ---
Route::get('/debug-session', function (Request $request) {
    return [
        'user'    => Auth::user(),
        'session' => $request->session()->all(),
    ];
});

// --- SPA Fallback (Vue handles routing) ---
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|auth).*$');

Route::get('/debug-auth', function (Request $request) {
    return [
        'auth_check'   => auth()->check(),
        'auth_user'    => auth()->user(),
        'session_all'  => $request->session()->all(),
        'cookies'      => $request->cookies->all(),
    ];
});

Route::get('/debug-sessions-file', function () {
    return [
        'session_cookie_name' => config('session.cookie'),
        'session_driver' => config('session.driver'),
    ];
});

