<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
     public function handle($request, Closure $next)
    {

        if(null === $request->bearerToken()){
            return response()->json(['error'=>"Unauthorized"]);
        }
        if (!auth('admin-api')->check() AND !auth()->check() ) {
            return response()->json([
                'message'=> 'User is unauthorised!'
            ], 401); 
        }
        // else

        $response = $next($request);

        // Perform action

        return $response;
    }

    // protected function redirectTo($request)
    // {
    //     if (! $request->expectsJson()) {
    //         return route('login');
    //     }
    // }
}
