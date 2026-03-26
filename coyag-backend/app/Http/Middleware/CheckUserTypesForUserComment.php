<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserTypesForUserComment
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
        if(Auth::user()->hasRole(['presidente', 'director.tecnologia', 'director.comercial', 'director.ejecutivo'])) {
            return $next($request);
        }
        else {
            return response()->json(['error' => 'Unauthorized - No tiene permisos dentro de este módulo'], 403);
        }
    }
}
