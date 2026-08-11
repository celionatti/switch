<?php

declare(strict_types=1);

/**
 * Switch Framework Autoloader Bootstrapper.
 * Registers Composer autoloader and App namespace loader.
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

// Register App namespace autoloader for application code in app/
spl_autoload_register(function (string $class) {
    if (str_starts_with($class, 'App\\')) {
        $file = __DIR__ . '/../app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Optional development fallback autoloader (ignored in production skeleton)
if (!class_exists(\Switch\Kernel\App::class) && file_exists(__DIR__ . '/dev-autoload.php')) {
    require_once __DIR__ . '/dev-autoload.php';
}
