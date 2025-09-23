<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::updateOrCreate(
                ['email' => $googleUser->email],
                [
                    'name' => $googleUser->name,
                    'password' => Hash::make(Str::random(24)),
                    'google_id' => $googleUser->id,
                ]
            );

            // If user has no role, redirect to role selection
            if (!$user->hasAnyRole(['admin', 'teacher', 'student'])) {
                Auth::login($user);
                session()->regenerate();
                return redirect()->route('register.choose_role');
            }

            Auth::login($user);
            // Regenerate session to prevent fixation and ensure new cookie
            session()->regenerate();

            return redirect()->intended('/dashboard');
    /**
     * Show the role selection page after Google login.
     */
    public function showRoleSelection(Request $request)
    {
        $user = Auth::user();
        // If user already has a role, redirect to dashboard
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
        if ($user && !$user->hasAnyRole(['admin', 'teacher', 'student'])) {
            $user->assignRole($request->input('role'));
        }
        return redirect('/dashboard');
    }
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google authentication failed');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
