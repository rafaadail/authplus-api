<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        
        $exceptions->render(function (AuthenticationException $e, $request) {
            $request->headers->set('Accept', 'application/json');
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        });


        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'HTTP Error',
                ], $e->getStatusCode());
            }
        });


        $exceptions->render(function (\Throwable $e, $request) {

            if ($request->is('api/*')) {

                $status = 500;
                if ($e instanceof HttpExceptionInterface) {
                    $status = $e->getStatusCode();
                }

                return response()->json([
                    'success' => false,
                    'message' => config('app.debug')
                        ? $e->getMessage()
                        : 'Internal Server Error',
                ], $status);
            }
        });
    })->create();
