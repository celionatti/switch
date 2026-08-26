<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'default' => 'web',

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'storage_key' => 'api_token',
        ],
    ],

    'providers' => [
        'users' => [
            'model' => App\Models\User::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Passwordless Authentication (Magic Links & Recovery)
    |--------------------------------------------------------------------------
    |
    | Passwordless authentication allows users to sign in, register, and recover
    | their accounts via secure, single-use, time-limited magic links sent to their email.
    |
    */

    'passwordless' => [
        'token_expiry' => 15,          // Minutes for sign-in & registration links
        'recovery_expiry' => 60,       // Minutes for account recovery links
        'token_length' => 64,          // Token entropy length (hex chars)
        'verify_route' => '/auth/verify',
        'rate_limit' => [
            'enabled' => true,
            'max_attempts' => 5,       // Max link requests per email
            'decay_seconds' => 3600,   // Per 1-hour window
        ],
        'auto_register' => false,      // Auto-create user account if unknown email attempts login
    ],
];
