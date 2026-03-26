<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTypeOfUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $type): Response
    {
        if($type == "cliente") {
            if(Auth::user()->client) return $next($request);
        }

        if($type == "empleado") {
            if(Auth::user()->employee) return $next($request);
        }

        return response()->json(['error' => 'Unauthorized - Tipo de Usuario no válido - 1'], 403);
    }
}
