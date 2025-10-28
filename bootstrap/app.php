<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\PreventDoubleBooking;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Http\Controllers\API\BaseController;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'prevent.double.booking' => PreventDoubleBooking::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
      

        // 403 from Symfony AccessDeniedHttpException
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
             return app(BaseController::class)->sendError('Unauthorized',['errors' => $e->getMessage()],403);
            }
        });

        // 401 for unauthenticated
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
               return app(BaseController::class)->sendError('Unauthenticated',['errors' => $e->getMessage()],401);
            }
        });
    })->create();
