<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('token');

        try{
            if($token){
                $user = JWTAuth::setToken($token)->authenticate();
                if($user){
                    return $next($request);
                }
            }
        }
        catch(JWTException $e){
            return response()->json(['error' => 'Invalid token', 'exception' => $e->getMessage()], 401)->cookie(
                'token',
                '', // Valor vacío
                -1, // Tiempo negativo para expirar inmediatamente
            );
        }
        return response()->json(['error' => 'Unauthorized'], 401)->cookie(
            'token',
            '', // Valor vacío
            -1, // Tiempo negativo para expirar inmediatamente
        );
    }
}
