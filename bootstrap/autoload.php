<?php

declare(strict_types=1);

/**
 * Switch Framework Autoloader Bootstrapper.
 * Handles Composer autoloading and development fallback maps.
 */

$autoloadPaths = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
];

foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}

// Register App namespace autoloader
spl_autoload_register(function (string $class) {
    if (str_starts_with($class, 'App\\')) {
        $file = __DIR__ . '/../app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Register Switch package fallback autoloader for development
if (!class_exists(\Switch\Kernel\App::class)) {
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
