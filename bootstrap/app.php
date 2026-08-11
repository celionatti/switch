<?php

declare(strict_types=1);

use Switch\Kernel\App;

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
    ->withMiddleware(function ($middleware) {
        // Register global or route middleware here
    })
    ->withExceptions(function ($exceptions) {
        // Register custom exception handling here
    })
    ->create();
