<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class AgentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return Redirect::route('login')->with('error', 'Please login to access this page.');
        }

        // Check if user is registered as an agent
        $user = Auth::user();

        if (!$user->agent) {
            if ($user->isAdmin()) {
                return Redirect::route('admin')->with('error', 'You must be registered as an agent to access this page.');
            } else {
                return Redirect::route('dashboard')->with('error', 'You must be registered as an agent to access this page.');
            }
        }

        return $next($request);
    }
}
