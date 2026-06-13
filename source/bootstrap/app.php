<?php

use App\Exceptions\Handler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\ValidateClientKey;
use App\Http\Middleware\TrustProxies;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            // Load web routes
            Route::middleware(['web'])
                ->group(__DIR__.'/../routes/web.php');
            // Load all API version route files
            $routeFiles = glob(__DIR__.'/../routes/api_v*.php');
            foreach ($routeFiles as $routeFile) {
                Route::middleware(['api'])
                    ->group($routeFile);
            }
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        App\Providers\RepositoryServiceProvider::class,
        App\Providers\EventServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->use([
            HandleCors::class,
            TrustProxies::class,
        ]);
        $middleware->alias([
            'auth' => Authenticate::class,
            'client.key' => ValidateClientKey::class,
            'inertia' => HandleInertiaRequests::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $handler = new Handler(app());
        $exceptions->render(function (Throwable $e, $request) use ($handler) {
            return $handler->render($request, $e);
        });
    })
    ->create();
