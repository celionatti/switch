<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Schema Diagram Explorer Enabled
    |--------------------------------------------------------------------------
    |
    | When set to true, the interactive live ER diagram explorer is active.
    | By default, it is strictly enabled in development mode and disabled
    | in production environments.
    |
    */
    'enabled' => env('DIAGRAM_ENABLED', env('APP_ENV', 'development') !== 'production'),

    /*
    |--------------------------------------------------------------------------
    | Route Path
    |--------------------------------------------------------------------------
    |
    | The base URI path where the interactive diagram and its API will be served.
    |
    */
    'route' => env('DIAGRAM_ROUTE', '/_diagram'),

    /*
    |--------------------------------------------------------------------------
    | Ignored Tables
    |--------------------------------------------------------------------------
    |
    | Database tables to hide from the visual diagram canvas.
    |
    */
    'ignore_tables' => [
        'migrations',
        'sqlite_sequence',
        'sessions',
    ],

    /*
    |--------------------------------------------------------------------------
    | Models Directories
    |--------------------------------------------------------------------------
    |
    | Directories to scan for ORM Models to detect virtual relations and scopes.
    |
    */
    'model_paths' => [
        __DIR__ . '/../app/Models',
    ],
];
