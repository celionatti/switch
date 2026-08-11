<?php

declare(strict_types=1);

define('SWITCH_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Autoloader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to initialize it!
|
*/

require_once __DIR__ . '/../bootstrap/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the developer-defined routes and send the response back.
|
*/

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->run();
