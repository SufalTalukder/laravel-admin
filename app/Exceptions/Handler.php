<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception): \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
    {
        if ($exception instanceof NotFoundHttpException && $request->is('api/*')) {
            return response()->json([
                'status' => 'Error',
                'message' => 'API endpoint not found.'
            ], 404);
        }

        return parent::render($request, $exception);
    }
}
