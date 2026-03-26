<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissionByRoleType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {

        $type = explode('-', $roles);

        if(Auth::user()->hasRole($type))
            return $next($request);
        else
            return response()->json(['error' => 'Unauthorized - Tipo de Usuario no válido - 2'], 403);
    }
}
