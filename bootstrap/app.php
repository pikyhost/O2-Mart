<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            HandleCors::class,
          //  \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'throttle.auth' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':auth',
            'throttle.forms' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':forms',
            'throttle.checkout' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':checkout',
            'throttle.search' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':search',
            'throttle.cart' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':cart',
            // 'api.security' => \App\Http\Middleware\ApiSecurityMiddleware::class,
        ]);

        // Add security headers to all requests
      //  $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);

        // API middleware group
     //   $middleware->prependToGroup('api', \App\Http\Middleware\AlwaysAcceptJson::class);
      //  $middleware->prependToGroup('api', \App\Http\Middleware\ValidateJsonMiddleware::class);

//        $middleware->prependToGroup('api', \App\Http\Middleware\CheckAuthOrSession::class);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Validation exceptions
        $exceptions->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // General API exceptions
        $exceptions->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return match (true) {
                    $e instanceof NotFoundHttpException => response()->json([
                        'status' => 'error',
                        'message' => 'Endpoint not found.',
                    ], 404),
                    $e instanceof MethodNotAllowedHttpException => response()->json([
                        'status' => 'error',
                        'message' => 'HTTP method not allowed.',
                    ], 405),
                    $e instanceof ModelNotFoundException => response()->json([
                        'status' => 'error',
                        'message' => 'Resource not found.',
                    ], 404),
                    default => response()->json([
                        'status' => 'error',
                        'message' => $e->getMessage(),
                    ], method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500),
                };
            }
        });
    })
    ->create();
