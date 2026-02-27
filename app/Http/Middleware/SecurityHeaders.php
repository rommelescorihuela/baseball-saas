<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (method_exists($response, 'header')) {
            $response->header('X-Frame-Options', 'SAMEORIGIN'); // Prevent Clickjacking (Except for same domain if needed)
            $response->header('X-Content-Type-Options', 'nosniff'); // Prevent MIME-sniffing
            $response->header('X-XSS-Protection', '1; mode=block'); // Legacy XSS Protection just in case
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload'); // HSTS (Force HTTPS)
            // Note: Content-Security-Policy (CSP) is harder to set globally in Filament due to inline scripts, so we skip rigid CSP for now.
        }

        return $response;
    }
}
