<?php

declare(strict_types=1);

use Switch\Kernel\App;
use Switch\Kernel\Config\MiddlewareCollector;
use Switch\Kernel\Config\ExceptionsCollector;

/*
|--------------------------------------------------------------------------
| Create The Switch Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Switch application instance
| which serves as the glue for all components of Switch Framework.
|
| Canonical Application Directory Structure:
| - app/Actions/      -> Single-responsibility Domain Actions (make:action)
| - app/Controllers/  -> Slim HTTP & API Controllers (make:controller)
| - app/Models/       -> Database ORM Models & Entities (make:model)
| - app/Services/     -> Domain & Business Logic Services
| - app/Utils/        -> Auto-discovered Custom Functions & Classes
| - app/Middleware/   -> Custom PSR-15 Application Middleware (make:middleware)
| - app/Providers/    -> Application Service Providers (make:provider)
| - app/Mail/         -> Email Mailables & Notifications (make:mail)
| - app/Events/       -> Application Events & Listeners
|
*/

return App::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
    )
    ->withMiddleware(function (MiddlewareCollector $middleware) {
        // Global Middleware (applied to every incoming HTTP request):
        $middleware->append(\Switch\Kernel\Middleware\SecurityHeadersMiddleware::class);

        // Web Route Group Middleware (Session, CSRF, Cookies, Sliding Expiration):
        $middleware->web(\Switch\Session\Middleware\StartSession::class);

        // Route Middleware Aliases:
        $middleware->alias([
            'auth' => \Switch\Foundation\Auth\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (ExceptionsCollector $exceptions) {
        // Custom error handling / reporting hooks:
        // $exceptions->report(fn(Throwable $e) => custom_log($e));
    })
    ->create();
