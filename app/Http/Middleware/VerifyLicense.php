<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyLicense
{
    /**
     * Keep requests flowing even when license verification is not configured.
     * This restores the middleware expected by the bootstrap pipeline so the
     * installer and app routes do not fail with a missing class error.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
