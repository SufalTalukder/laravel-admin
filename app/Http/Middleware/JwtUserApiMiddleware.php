<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtUserApiMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('accessToken');

        if (!$token) {
            return response()->json([
                'status'  => 'Unauthorized',
                'message' => 'Access token is missing.',
            ], 401);
        }

        try {
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();

            if ($payload->get('is_refresh') === true) {
                return response()->json([
                    'status'  => 'Unauthorized',
                    'message' => 'Refresh token cannot be used as an access token.',
                ], 401);
            }

            $request->merge([
                'user_id'           => $payload->get('user_id'),
                'user_phone_number' => $payload->get('phone_number'),
                'user_type'         => $payload->get('user_type'),
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'status'  => 'Token Expired',
                'message' => 'Access token expired. Use your refresh token to get a new one.',
                'code'    => 'ACCESS_TOKEN_EXPIRED',
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'status'  => 'Unauthorized',
                'message' => 'Token is invalid.',
                'code'    => 'TOKEN_INVALID',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'status'  => 'Unauthorized',
                'message' => 'Token error: ' . $e->getMessage(),
            ], 401);
        }

        return $next($request);
    }
}
