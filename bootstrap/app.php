<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle authentication errors for API
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }
        });

        // Handle authorization errors for API (Laravel)
        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'This action is unauthorized.',
                ], 403);
            }
        });

        // Handle authorization errors for API (Symfony)
        $exceptions->render(function (AccessDeniedHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'This action is unauthorized.',
                ], 403);
            }
        });

        // Handle model not found errors for API
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Resource not found.',
                ], 404);
            }
        });

        // Handle general 404 errors for API
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'The requested resource was not found.',
                ], 404);
            }
        });

        // Handle general server errors for API
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                // Only handle 500 errors here (other status codes handled above)
                if ($statusCode === 500) {
                    return response()->json([
                        'message' => app()->environment('production')
                            ? 'Server error occurred.'
                            : $e->getMessage(),
                        'exception' => app()->environment('production') ? null : get_class($e),
                        'file'      => app()->environment('production') ? null : $e->getFile(),
                        'line'      => app()->environment('production') ? null : $e->getLine(),
                    ], 500);
                }
            }
        });
    })->create();
