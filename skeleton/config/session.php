<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Session Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "file", "database", "cookie", "array"
    |
    */
    'driver' => env('SESSION_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime (Minutes)
    |--------------------------------------------------------------------------
    |
    | Number of minutes the session should remain valid.
    | Switch Framework uses sliding session expiration: each active request
    | automatically extends the session window into the future.
    |
    */
    'lifetime' => (int) env('SESSION_LIFETIME', 120),

    /*
    |--------------------------------------------------------------------------
    | Session Expiration On Close
    |--------------------------------------------------------------------------
    |
    | If set to true, the session cookie expires immediately when the browser closes.
    | If false, the session lasts for the duration specified in 'lifetime'.
    |
    */
    'expire_on_close' => (bool) env('SESSION_EXPIRE_ON_CLOSE', false),

    /*
    |--------------------------------------------------------------------------
    | Session File Storage Location
    |--------------------------------------------------------------------------
    |
    | Storage path for file-based session handler.
    |
    */
    'files' => dirname(__DIR__) . '/storage/sessions',

    /*
    |--------------------------------------------------------------------------
    | Session Database Table
    |--------------------------------------------------------------------------
    |
    | Table name when using the "database" session driver.
    |
    */
    'table' => env('SESSION_TABLE', 'sessions'),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Name
    |--------------------------------------------------------------------------
    |
    | Name of the session cookie transmitted to the client.
    |
    */
    'cookie' => env('SESSION_COOKIE', 'switch_session'),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Path
    |--------------------------------------------------------------------------
    |
    | Path on the domain where the session cookie will be available.
    |
    */
    'path' => env('SESSION_PATH', '/'),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Domain
    |--------------------------------------------------------------------------
    |
    | Domain for which the session cookie is valid.
    |
    */
    'domain' => env('SESSION_DOMAIN', null),

    /*
    |--------------------------------------------------------------------------
    | HTTPS Only Cookies
    |--------------------------------------------------------------------------
    |
    | Send cookie only over HTTPS connections.
    |
    */
    'secure' => (bool) env('SESSION_SECURE_COOKIE', false),

    /*
    |--------------------------------------------------------------------------
    | HTTP-Only Cookies
    |--------------------------------------------------------------------------
    |
    | Prevent client-side JavaScript from accessing the session cookie (XSS protection).
    |
    */
    'http_only' => true,

    /*
    |--------------------------------------------------------------------------
    | Same-Site Cookie Policy
    |--------------------------------------------------------------------------
    |
    | Protect against CSRF attacks. Supported: "lax", "strict", "none", null.
    |
    */
    'same_site' => env('SESSION_SAME_SITE', 'lax'),
];
