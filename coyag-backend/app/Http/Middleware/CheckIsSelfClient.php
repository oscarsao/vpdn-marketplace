<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIsSelfClient
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
        if( Auth::user()->client->id == $request->route()->parameter('idClient') ) {
            return $next($request);
        }
        else {
            return response()->json(['error' => 'Unauthorized - Recurso no le pertenece'], 403);
        }
    }
}
