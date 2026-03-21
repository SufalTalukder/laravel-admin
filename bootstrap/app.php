<?php

use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Middleware\JwtAuthApiMiddleware;
use App\Http\Middleware\JWTAuthMiddleware;
use App\Http\Middleware\JwtUserApiMiddleware;
use App\Http\Middleware\RedirectIfAuthenticatedJWT;
use App\Http\Middleware\ThrottleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.jwt'  => JWTAuthMiddleware::class,
            'guest.jwt' => RedirectIfAuthenticatedJWT::class,
            'throttle.admin' => ThrottleMiddleware::class,
            'api.key' => ApiKeyMiddleware::class,
            'jwt.cookie' => JwtAuthApiMiddleware::class,
            'jwt.user' => JwtUserApiMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
