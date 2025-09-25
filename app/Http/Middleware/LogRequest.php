<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LogRequest
{
    public function handle(Request $request, Closure $next)
    {
        \Log::debug('Request info', [
            'uri' => $request->getRequestUri(),
            'session_id' => session()->getId(),
            'has_session' => session()->isStarted(),
            'auth_check' => auth()->check(),
            'cookies' => collect($request->cookies->all())->keys(),
        ]);

        $response = $next($request);

        \Log::debug('Response info', [
            'status' => $response->getStatusCode(),
            'headers' => collect($response->headers->all())->keys(),
        ]);

        return $response;
    }
}