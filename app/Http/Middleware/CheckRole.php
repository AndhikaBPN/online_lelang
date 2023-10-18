<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){ 
                return response()->json(['status' => 'Token is Invalid'], 401); 
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){ 
                return response()->json(['status' => 'Token is Expired'], 401); 
            }else{ 
                return response()->json(['status' => 'Authorization Token not found'], 401); 
            }
        }

        foreach($roles as $role){
            if($request->user()->role == $role){
                return $next($request);
            }
        }
        return $this->unauthorized();
    }

    public function unauthorized($message = null){
        return response()->json([
            'success' => false,
            'message' => $message ? $message : 'Anda tidak memiliki akses',
        ], 401);
    }
}
