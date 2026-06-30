<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'landlord.approved' => \App\Http\Middleware\EnsureLandlordApproved::class,
            'admin'             => \App\Http\Middleware\EnsureAdmin::class,

            // ✅ NEW: role middleware
            'role'              => \App\Http\Middleware\EnsureRole::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
