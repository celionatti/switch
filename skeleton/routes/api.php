<?php

declare(strict_types=1);

use Switch\Router\Facade\Route;

Route::get('/status', function () {
    return [
        'status' => 'ok',
        'framework' => 'Switch Framework',
        'version' => '1.0.0',
        'timestamp' => time(),
    ];
})->name('status');