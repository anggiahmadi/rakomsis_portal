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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'agent' => \App\Http\Middleware\AgentMiddleware::class,
            'customer' => \App\Http\Middleware\CustomerMiddleware::class,
            'employee' => \App\Http\Middleware\EmployeeMiddleware::class,
        ]);

        // Use custom CSRF token middleware to exclude xendit webhook
        $middleware->replace(
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
