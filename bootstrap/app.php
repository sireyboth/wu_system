<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // This is where you would "talk" to the TrimStrings logic
        // even though the file doesn't exist in your app folder.
        $middleware->trimStrings(except: [
            'some_special_field',
        ]);

        $middleware->alias([
            'role'         => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'   => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // The API is called same-origin from the same session-authenticated
        // pages (no token-based client exists), so it needs the 'web' group
        // for auth()/session to work — permission checks otherwise have no
        // logged-in user to check against. No JS here sends a CSRF header,
        // so api/* stays exempt rather than breaking every write endpoint.
        $middleware->group('api', ['web']);
        $middleware->validateCsrfTokens(except: ['api/*']);
    })
    ->withExceptions(function ($exceptions) {
    $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
        return no_data('Record not found', 404);
    });
})->create();
