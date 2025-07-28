<?php

use App\Http\Middleware\CheckFeatureMiddleware;
use App\Http\Middleware\CheckSubscriptionMiddleware;
use App\Http\Middleware\SubscriptionMiddleware;
use App\Http\Middleware\TenantMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->append(SubscriptionMiddleware::class);
        // $middleware->append(AdminMiddleware::class);
        // $middleware->append(TenantMiddleware::class);
        // $middleware->append(CheckSubscriptionMiddleware::class);
        // $middleware->append(CheckFeatureMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
