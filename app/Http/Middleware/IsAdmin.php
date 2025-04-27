<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is not an admin
        if (!$request->user() || !$request->user()->isAdmin()) {

            // If not, redirect to the previous page with error message
            return redirect()->back()->with('error', 'You do not have Admin access.');
        }
        
        return $next($request);
    }
}
