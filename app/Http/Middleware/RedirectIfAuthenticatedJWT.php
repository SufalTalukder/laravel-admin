<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class RedirectIfAuthenticatedJWT
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->cookie('auth_token');

            if ($token) {
                $user = JWTAuth::setToken($token)->authenticate();

                if ($user && $request->routeIs('adminLoginViewPage')) {
                    return redirect()->route('adminDashboard');
                }
            }
        } catch (\Exception $e) {
            // ignore
        }

        return $next($request);
    }
}
