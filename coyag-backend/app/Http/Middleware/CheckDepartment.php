<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckDepartment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $department): Response
    {
        if(Auth::user()->employee->department->name == $department) {
            return $next($request);
        }
        else {
            return response()->json(['error' => 'Unauthorized - Su departamento no tiene permiso de Acceso'], 403);
        }
    }
}
