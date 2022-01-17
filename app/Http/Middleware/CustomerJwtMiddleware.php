<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomerJwtMiddleware
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
        
         if (!auth('customer-api')->check()) {
            return response()->json([
                'message'=> 'Customer is unauthorised!'
            ], 401); 
        }
        return $next($request);
        
    }
}
    