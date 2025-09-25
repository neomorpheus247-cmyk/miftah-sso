<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DebugGoogleAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Log pre-request state
        \Log::debug('Google Auth Request', [
            'path' => $request->path(),
            'session_id' => session()->getId(),
            'session_started' => session()->isStarted(),
            'auth_check' => auth()->check(),
            'middleware' => array_values(Route::current()->middleware()),
            'cookies_received' => collect($request->cookies->all())->keys()->toArray()
        ]);

        $response = $next($request);

        // Log post-response state
        \Log::debug('Google Auth Response', [
            'path' => $request->path(),
            'status' => $response->getStatusCode(),
            'session_id' => session()->getId(),
            'auth_check' => auth()->check(),
            'cookies_sent' => collect($response->headers->getCookies())->map->getName()->toArray(),
            'headers' => collect($response->headers->all())->keys()->toArray()
        ]);

        return $response;
    }
}