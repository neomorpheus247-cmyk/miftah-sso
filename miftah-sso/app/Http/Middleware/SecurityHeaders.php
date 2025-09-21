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
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()');
        
        // Only add HSTS header if we're on HTTPS
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Content Security Policy
        $cspHeader = "default-src 'self'; " .
                    "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://accounts.google.com https://apis.google.com; " .
                    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
                    "img-src 'self' data: https:; " .
                    "font-src 'self' https://fonts.gstatic.com; " .
                    "frame-src https://accounts.google.com; " .
                    "connect-src 'self' https://apis.google.com; " .
                    "object-src 'none'; " .
                    "base-uri 'self'; " .
                    "form-action 'self';";

        $response->headers->set('Content-Security-Policy', $cspHeader);

        return $response;
    }
}