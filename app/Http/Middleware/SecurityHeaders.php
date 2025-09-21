<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Content Security Policy - Simplified for Google OAuth
        $cspHeader = "default-src 'self'; " .
                    "script-src 'self' https://accounts.google.com; " .
                    "frame-src https://accounts.google.com; " .
                    "connect-src 'self' https://apis.google.com;";

        $response->headers->set('Content-Security-Policy', $cspHeader);

        return $response;
    }
}