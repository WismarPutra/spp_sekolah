<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika tidak login ATAU login tapi bukan admin
        if (!Auth::check() || Auth::user()->role !== 'orang_tua') {
            return redirect('login')->with('error', 'Akses khusus Orang Tua!');
        }

        return $next($request);
    }
}