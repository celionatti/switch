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
if (is_dir($configPath)) {
    $config = new Config($configPath);
}

// 2. Initialize Database Connection if configured
$dbFile = $basePath . '/database/database.sqlite';
if (file_exists($dbFile) || is_dir(dirname($dbFile))) {
    if (!is_dir(dirname($dbFile))) {
        mkdir(dirname($dbFile), 0777, true);
    }
    $connection = Connection::sqlite($dbFile);
    ConnectionManager::addConnection('default', $connection);
}

// 3. Initialize View Engine
$viewsPath = $basePath . '/resources/views';
$cachePath = $basePath . '/storage/views';
if (is_dir($viewsPath)) {
    $viewEngine = new ViewEngine($viewsPath, $cachePath);
    View::setEngine($viewEngine);
}

// 4. Create App Kernel
$app = App::create();
$app->setBasePath($basePath);

return $app;
