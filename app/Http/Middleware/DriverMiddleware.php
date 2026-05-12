<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DriverMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (auth()->user()->role === 'driver' || auth()->user()->role === 'admin')) {
            return $next($request);
        }

        return redirect()->route('landing')->with('error', 'Unauthorized access to Driver Co-Pilot.');
    }
}
