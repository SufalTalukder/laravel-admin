<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('accessToken');

        if (!$token) {
            return response()->json([
                'status'  => 'Unauthorized',
                'message' => 'Unauthorized access.',
            ], 401);
        }

        try {
            JWTAuth::setToken($token);

            // Get claims from token
            $payload = JWTAuth::getPayload();

            $request->merge([
                'jwt_auth_user_id'   => $payload->get('auth_user_id'),
                'jwt_auth_user_type' => $payload->get('auth_user_type'),
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status'  => 'Unauthorized',
                'message' => 'Token error: ' . $e->getMessage(),
            ], 401);
        }

        return $next($request);
    }
}
