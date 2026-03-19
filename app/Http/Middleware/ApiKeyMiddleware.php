<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey   = $request->header('X-API-KEY');
        $apiToken = $request->header('X-API-TOKEN');

        if ($apiKey !== env('X_API_KEY') || $apiToken !== env('X_API_TOKEN')) {
            return response()->json([
                'status'  => 'Unauthorized',
                'message' => 'Unauthorized access.',
            ], 401);
        }

        return $next($request);
    }
}
