<?php
namespace App\Http\Middleware;

use Closure;

class NoStoreForHtml
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Only for HTML or error responses
        $isHtml = str_contains($response->headers->get('Content-Type', ''), 'text/html');
        $isError = $response->getStatusCode() >= 400;

        if ($isHtml || $isError) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}