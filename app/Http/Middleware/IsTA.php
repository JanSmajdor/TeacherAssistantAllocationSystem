<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsTA
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is a TA
        if (!$request->user() || !$request->user()->isTA()) {
            // If not, redirect to the home page or show an error
            return redirect()->back()->with('error', 'You do not have Teaching Assistant access.');
        }

        // Proceed with the request if the user is a TA
        return $next($request);
    }
}
