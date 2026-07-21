<?php

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
        $middleware->trimStrings(except: [
            'some_special_field',
        ]);
    })
    ->withExceptions(function ($exceptions) {
    $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
        return no_data('Record not found', 404);
    });
})->create();
