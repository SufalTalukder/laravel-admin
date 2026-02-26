<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class JWTAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->cookie('auth_token');

            if (!$token) {
                return redirect()->route('adminLoginViewPage');
            }

            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return redirect()->route('adminLoginViewPage');
            }

            Auth::setUser($user);
        } catch (\Exception $e) {
            return redirect()->route('adminLoginViewPage');
        }

        return $next($request);
    }
}
