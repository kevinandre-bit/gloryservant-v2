<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        // Generate a per-request nonce for CSP (base64 for compatibility)
        $nonce = base64_encode(random_bytes(16));

        // Make nonce available to views and the container
        View::share('cspNonce', $nonce);
        app()->instance('csp_nonce', $nonce);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        // Build CSP with nonce and allowlisted CDNs used by the app.
        $scriptSrc = ["'self'", "'nonce-{$nonce}'"];
        // Keep inline <style> blocks guarded by nonce; allow style attributes via style-src-attr below
        // Allow inline <style> safely across browsers (CSP3 note: when a nonce is present
        // in style-src, browsers ignore 'unsafe-inline'). To ensure compatibility with
        // libraries that inject <style> or apply inline styles, do NOT include the nonce
        // in style-src. We still keep granular attr/elem policies below.
        $styleSrc  = ["'self'", "'unsafe-inline'"];
        $fontSrc   = ["'self'", 'data:'];

        // Defaults for commonly used CDNs in this app
        $defaultScriptCDNs = ['https://unpkg.com', 'https://cdn.jsdelivr.net', 'https://www.gstatic.com'];
        $defaultStyleCDNs  = ['https://fonts.googleapis.com', 'https://cdn.jsdelivr.net', 'https://cdnjs.cloudflare.com'];
        $defaultFontCDNs   = ['https://fonts.gstatic.com', 'https://cdn.jsdelivr.net', 'https://cdnjs.cloudflare.com'];

        // Extend via env (comma-separated)
        $extraScript = array_filter(array_map('trim', explode(',', (string) env('CSP_SCRIPT_SRC', ''))));
        $extraStyle  = array_filter(array_map('trim', explode(',', (string) env('CSP_STYLE_SRC', ''))));
        $extraFont   = array_filter(array_map('trim', explode(',', (string) env('CSP_FONT_SRC', ''))));

        $scriptSrc = array_values(array_unique(array_merge($scriptSrc, $defaultScriptCDNs, $extraScript)));
        $styleSrc  = array_values(array_unique(array_merge($styleSrc,  $defaultStyleCDNs,  $extraStyle)));
        $fontSrc   = array_values(array_unique(array_merge($fontSrc,   $defaultFontCDNs,   $extraFont)));

        $directives = [
            "default-src 'self'",
            "base-uri 'self'",
            "frame-ancestors 'none'",
            "object-src 'none'",
            "form-action 'self'",
            'script-src ' . implode(' ', $scriptSrc),
            'style-src '  . implode(' ', $styleSrc),
            "img-src 'self' data:",
            'font-src '   . implode(' ', $fontSrc),
            "connect-src 'self'",
            "media-src 'self'",
        ];

        // Allow inline style attributes explicitly (CSP3)
        $directives[] = "style-src-attr 'unsafe-inline'";
        // Allow <style> elements and external stylesheets from self and trusted CDNs; permit inline content
        $directives[] = "style-src-elem 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com";

        $cspValue = implode('; ', $directives);

        // Use Report-Only mode when explicitly enabled via env
        $reportOnly = (bool) env('CSP_REPORT_ONLY', false);
        $headerName = $reportOnly ? 'Content-Security-Policy-Report-Only' : 'Content-Security-Policy';

        $response->headers->set($headerName, $cspValue, false);

        // Additional recommended security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff', false);
        $response->headers->set('Referrer-Policy', 'no-referrer', false);
        // Use a modern, recognized subset for Permissions-Policy (remove deprecated features)
        $response->headers->set('Permissions-Policy',
            implode(', ', [
                'geolocation=()',
                'microphone=()',
                'camera=()',
                'fullscreen=()',
                'payment=()',
                'publickey-credentials-get=()'
            ]),
        false);
        // Legacy fallback for frame protection
        $response->headers->set('X-Frame-Options', 'DENY', false);

        // Expose nonce via header for debugging (disabled by default)
        if ((bool) env('CSP_EXPOSE_NONCE_HEADER', false)) {
            $response->headers->set('X-CSP-Nonce', $nonce, false);
        }

        return $response;
    }
}
