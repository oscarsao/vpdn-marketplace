<?php

namespace App\Http\Middleware;

use App\Traits\ClientTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveService
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $frontend): Response
    {
        /**
         * Solo los empleados y los clientes con un plan activo
         * especificado pueden tener acceso al recurso solicitado
         */

        if(isset(Auth::user()->employee->id))
            return $next($request);


        if(!ClientTrait::checkActiveServiceTypeFrontend(Auth::user()->client, $frontend))
            return response()->json(['error' => 'No tiene un servicio activo de ' . ucfirst($frontend)], 403);


        return $next($request);  // AQUI
    }
}
