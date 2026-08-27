<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'Switch Framework'),
    'env' => env('APP_ENV', 'development'),
    'debug' => env('APP_DEBUG', true),
    'url' => env('APP_URL', 'http://localhost:8000'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded during
    | application bootstrapping to bind interfaces and boot services.
    |
    */
    'providers' => [
        App\Providers\AppServiceProvider::class,
    ],
];
