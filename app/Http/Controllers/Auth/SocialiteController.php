<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
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

            $token = JWTAuth::fromUser($user);

            // If user has no role, redirect to role selection with token
            if (!$user->hasAnyRole(['admin', 'teacher', 'student'])) {
                return redirect()->route('register.choose_role')->with('token', $token);
            }

            // Return token and user info to frontend
            return response()->json([
                'token' => $token,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Google authentication failed'], 401);
        }
    }

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

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
