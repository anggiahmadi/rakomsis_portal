<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return Redirect::route('login')->with('error', 'Please login first.');
        }

        $user = Auth::user();

        if (!$user->isAdmin()) {
            return Redirect::route('dashboard')->with('error', 'You are not authorized to access admin area.');
        }

        return $next($request);
    }
}
