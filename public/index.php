<?php

declare(strict_types=1);

define('SWITCH_START', microtime(true));

// Autoload Composer Dependencies
$autoloadFile = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoloadFile)) {
    require_once $autoloadFile;
} else {
    // Fallback monorepo autoloader for development
    spl_autoload_register(function (string $class) {
        $map = [
            'Switch\\Container\\' => __DIR__ . '/../../packages/container/src/',
            'Switch\\Http\\' => __DIR__ . '/../../packages/http-message/src/',
            'Switch\\Router\\' => __DIR__ . '/../../packages/router/src/',
            'Switch\\Event\\' => __DIR__ . '/../../packages/events/src/',
            'Switch\\Config\\' => __DIR__ . '/../../packages/config/src/',
            'Switch\\Kernel\\' => __DIR__ . '/../../packages/kernel/src/',
            'Switch\\View\\' => __DIR__ . '/../../packages/view/src/',
            'Switch\\Database\\' => __DIR__ . '/../../packages/database/src/',
            'Switch\\ErrorHandler\\' => __DIR__ . '/../../packages/error-handler/src/',
            'Switch\\Console\\' => __DIR__ . '/../../packages/console/src/',
            'App\\' => __DIR__ . '/../app/',
        ];

        foreach ($map as $prefix => $dir) {
            if (str_starts_with($class, $prefix)) {
                $relativeClass = str_replace($prefix, '', $class);
                $file = $dir . str_replace('\\', '/', $relativeClass) . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        }
    });
}

// Register Error Handler
if (class_exists(\Switch\ErrorHandler\ErrorHandler::class)) {
    $errorHandler = \Switch\ErrorHandler\ErrorHandler::register();
    $errorHandler->setDebug(true);
}

// Bootstrap Application Kernel
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Handle Request
$app->run();
