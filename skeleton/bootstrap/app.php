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
| which serves as the glue for all components of Switch.
|
*/

return App::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
    )
    ->withMiddleware(function (MiddlewareCollector $middleware) {
        // Example: $middleware->append(MyGlobalMiddleware::class);
    })
    ->withExceptions(function (ExceptionsCollector $exceptions) {
        // Example: $exceptions->report(fn(Throwable $e) => custom_log($e));
    })
    ->create();
