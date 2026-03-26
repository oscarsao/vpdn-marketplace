<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIsSelfClientOrEmployee
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

        if(isset(Auth::user()->client->id))
        {
            if(Auth::user()->client->id == $request->route()->parameter('idClient'))
                return $next($request);
        }

        if(Auth::user()->employee)
            return $next($request);


        return response()->json(['error' => 'Unauthorized - Recurso no le pertenece'], 403);

    }
}
