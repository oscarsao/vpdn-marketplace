<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIsSelfEmployee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::user()->employee->id == $request->route()->parameter('idEmployee'))
            return $next($request);

        return response()->json(['error' => 'Unauthorized - Tipo de Usuario no válido - 3'], 403);
    }
}
