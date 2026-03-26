<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMinimumLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, int $level): Response
    {
        if(Auth::user()->level() >= $level) {
            return $next($request);
        }
        else {
            return response()->json(['error' => 'Unauthorized - No tiene nivel suficiente'], 403);
        }
    }
}
