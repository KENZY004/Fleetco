<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'fleet_manager'])) {
            return $next($request);
        }

        // If not admin, redirect to tracking page
        return redirect()->route('track-me')->with('error', 'Unauthorized access to Admin Dashboard.');
    }
}
