<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminJwtMiddleware
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
        
         if (!auth('admin-api')->check()) {
            return response()->json([
                'message'=> 'User is unauthorised!'
            ], 401); 
        }
        return $next($request);
        
    }
}
