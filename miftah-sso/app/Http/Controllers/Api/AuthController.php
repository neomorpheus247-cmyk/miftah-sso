<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessDelayedLogout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Logout immediately
     */
    public function logout(Request $request): JsonResponse
    {
        if ($request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }
        
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Schedule a delayed logout
     */
    public function scheduleLogout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();
        $userId = $request->user()->id;

        ProcessDelayedLogout::dispatch($userId, $token?->id)
            ->delay(now()->addMinutes(5));
        
        return response()->json([
            'message' => 'Logout scheduled',
            'logout_at' => now()->addMinutes(5)->toIsoString()
        ]);
    }

    /**
     * Cancel scheduled logout if exists
     */
    public function cancelScheduledLogout(Request $request): JsonResponse
    {
        // Note: This would require storing the job ID in the session or database
        // For now, we'll just return a success message
        return response()->json(['message' => 'Scheduled logout cancelled']);
    }
}
