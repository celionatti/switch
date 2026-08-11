<?php

declare(strict_types=1);

use Switch\Kernel\App;
use Switch\Config\Config;
use Switch\Database\Connection\Connection;
use Switch\Database\Connection\ConnectionManager;
use Switch\View\Engine\ViewEngine;
use Switch\View\View;

$basePath = dirname(__DIR__);

// 1. Initialize Configuration
$configPath = $basePath . '/config';
$config = new Config();
if (is_dir($configPath)) {
    $config->loadFromDirectory($configPath);
}

// 2. Initialize Database Connection if configured
$dbFile = $basePath . '/database/database.sqlite';
if (file_exists($dbFile) || is_dir(dirname($dbFile))) {
    if (!is_dir(dirname($dbFile))) {
        mkdir(dirname($dbFile), 0777, true);
    }
    $connection = Connection::sqlite($dbFile);
    if (class_exists(\Switch\Database\ORM\Model::class)) {
        \Switch\Database\ORM\Model::setConnection($connection);
    }
}

// 3. Initialize View Engine
$viewsPath = $basePath . '/resources/views';
$cachePath = $basePath . '/storage/views';
if (is_dir($viewsPath)) {
    $viewEngine = new ViewEngine($viewsPath, $cachePath);
    View::setEngine($viewEngine);
}

// 4. Create App Kernel with Router singleton
$router = class_exists(\Switch\Router\Facade\Route::class)
    ? \Switch\Router\Facade\Route::getRouter()
    : null;

$app = new App(router: $router);
$app->setBasePath($basePath);

return $app;
