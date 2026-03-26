<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTypeOfClient
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

        if($type == 'premium') {
            if(Auth::user()->hasRole('usuario.premium.mayor') || Auth::user()->hasRole('usuario.premium.menor')) return $next($request);
        }

        if($type == 'standard') {
            if(Auth::user()->hasRole('usuario.estandar.mayor') || Auth::user()->hasRole('usuario.estandar.menor')) return $next($request);
        }

        if($type == 'lite') {
            if(Auth::user()->hasRole('usuario.lite')) return $next($request);
        }

        if($type == 'registered') {
            if(Auth::user()->hasRole('cliente.registrado')) return $next($request);
        }

        if($type == 'analysis-phase') {
            if(Auth::user()->hasRole('cliente.fase.evaluacion')) return $next($request);
        }

        if($type == 'evaluation-phase') {
            if(Auth::user()->hasRole('cliente.fase.analisis')) return $next($request);
        }

        if($type == 'execution-phase') {
            if(Auth::user()->hasRole('cliente.fase.ejecucion')) return $next($request);
        }

        if($type == 'comprehensive-counseling-phase') {
            if(Auth::user()->hasRole('cliente.fase.asesoramiento.integral')) return $next($request);
        }

        return response()->json(['error' => 'Unauthorized - Tipo de Cliente no válido'], 403);
    }
}
