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
        // Register your custom middleware aliases here
        $middleware->alias([
            'is_teacher' => \App\Http\Middleware\TeacherMiddleware::class,
            'is_student' => \App\Http\Middleware\StudentMiddleware::class,
        ]);

        // You can also add global middleware or middleware groups here if needed
        // For example:
        // $middleware->web(append: [
        //     \App\Http\Middleware\ForceJsonResponses::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();   