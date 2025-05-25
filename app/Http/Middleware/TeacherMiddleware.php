<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class TeacherMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Change to check role_id directly
        // Assuming role_id 2 is for 'guru'
        if (Auth::check() && Auth::user()->role_id === 2) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Akses ditolak. Anda bukan guru.');
    }
}