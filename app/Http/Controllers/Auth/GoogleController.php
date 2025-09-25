<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        // This is the first step, redirecting the user to Google.
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google and handle the login/registration.
     */
    public function handleGoogleCallback()
    {
        try {
            \Log::debug('Starting Google callback', [
                'session_id' => session()->getId(),
                'has_session' => session()->isStarted(),
                'cookies' => collect(request()->cookies->all())->keys(),
            ]);

            $googleUser = Socialite::driver('google')->user();
            \Log::debug('Retrieved Google user', [
                'email' => $googleUser->getEmail(),
                'id' => $googleUser->getId()
            ]);

            // Find an existing user by email or create a new one.
            // Using email as the primary key is more reliable as it can link
            // social logins to users who may have registered with a traditional password.
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    // A random password is set for users who only log in via social means.
                    // This is necessary as the 'password' field is often required.
                    'password' => Hash::make(Str::random(24)),
                ]
            );
            
            \Log::debug('User record state', [
                'user_id' => $user->id,
                'is_new' => $user->wasRecentlyCreated,
                'roles' => $user->roles->pluck('name'),
            ]);

            // Check if the user has a role assigned.
            // This is the core logic from your SocialiteController.
            if (!$user->hasAnyRole(['admin', 'teacher', 'student'])) {
                // If no role is assigned, log the user in temporarily and
                // redirect to the role selection page.
                Auth::login($user, true);
                \Log::debug('Logged in user without role', [
                    'session_id' => session()->getId(),
                    'auth_check' => Auth::check(),
                    'user_id' => Auth::id(),
                    'remember_token' => $user->getRememberToken(),
                    'cookies' => collect(request()->cookies->all())->keys(),
                ]);
                return redirect()->route('register.choose_role');
            }

            // If the user already has a role, log them in and redirect to the dashboard.
            Auth::login($user, true);
            \Log::debug('Logged in user with role', [
                'session_id' => session()->getId(),
                'auth_check' => Auth::check(),
                'user_id' => Auth::id(),
                'remember_token' => $user->getRememberToken(),
                'cookies' => collect(request()->cookies->all())->keys(),
            ]);
            return redirect()->intended('/dashboard')->with('status', 'Successfully logged in with Google!');

        } catch (Exception $e) {
            // Log detailed exception information
            \Log::error('Google authentication failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_id' => session()->getId(),
                'session_status' => session()->isStarted(),
                'auth_check' => Auth::check(),
                'cookies' => collect(request()->cookies->all())->keys(),
                'request_path' => request()->path(),
                'request_url' => request()->url(),
            ]);
            return redirect()->route('login')->with('error', 'Google authentication failed. Please try again.');
        }
    }

    /**
     * Show the role selection page after Google login.
     * This method is only for users who don't have a role yet.
     */
    public function showRoleSelection(Request $request)
    {
        $user = Auth::user();
        // If the user somehow gets to this page with a role, redirect them away.
        if ($user && $user->hasAnyRole(['admin', 'teacher', 'student'])) {
            return redirect('/dashboard');
        }
        return view('auth.choose_role');
    }

    /**
     * Handle role assignment after Google login.
     */
    public function registerRole(Request $request)
    {
        $request->validate([
            'role' => 'required|in:student,teacher',
        ]);
        
        $user = Auth::user();

        // Assign the role if the user exists and doesn't already have one.
        if ($user && !$user->hasAnyRole(['admin', 'teacher', 'student'])) {
            $user->assignRole($request->input('role'));
        }
        
        // Redirect to the dashboard after the role is assigned.
        return redirect('/dashboard');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
