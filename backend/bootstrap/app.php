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
        $middleware->redirectTo('/admin/login');

        // Trust all proxies (localtunnel/ngrok) so Laravel detects correct scheme & host
        $middleware->trustProxies(at: '*');

        // Bypass CSRF for stream URL updates API
        $middleware->validateCsrfTokens(except: [
            'api/streams/update-url',
        ]);

        // Register custom middleware aliases
        $middleware->alias([
            'api.key' => \App\Http\Middleware\ValidateApiKey::class,
            'subscription.active' => \App\Http\Middleware\CheckSubscription::class,
            'device.bind' => \App\Http\Middleware\CheckDeviceBinding::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
