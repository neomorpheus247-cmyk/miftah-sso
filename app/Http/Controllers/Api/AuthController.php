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

        $job = new ProcessDelayedLogout($userId, $token?->id);
        $jobId = app('queue.failer')->create(
            config('queue.delayed_logout_connection', 'database'),
            config('queue.delayed_logout_queue', 'delayed-logout'),
            json_encode($job)
        );

        $request->session()->put('scheduled_logout_job', $jobId);
        
        dispatch($job)->delay(now()->addMinutes(5));
        
        return response()->json([
            'message' => 'Logout scheduled',
            'logout_at' => now()->addMinutes(5)->toIsoString(),
            'job_id' => $jobId
        ]);
    }

    /**
     * Cancel scheduled logout if exists
     */
    public function cancelScheduledLogout(Request $request): JsonResponse
    {
        $jobId = $request->session()->get('scheduled_logout_job');
        
        if (!$jobId) {
            return response()->json(['message' => 'No scheduled logout found']);
        }

        // Remove the failed job
        app('queue.failer')->forget($jobId);
        $request->session()->forget('scheduled_logout_job');

        return response()->json(['message' => 'Scheduled logout cancelled']);
    }
}
