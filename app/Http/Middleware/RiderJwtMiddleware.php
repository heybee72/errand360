<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RiderJwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(null === $request->bearerToken()){
            return response()->json(['error'=>"Unauthorized"], 401);
        }
        
         if (!auth('rider-api')->check()) {
            return response()->json([
                'message'=> 'Rider is unauthorised!'
            ], 401); 
        }
        return $next($request);
        
    }
}
    