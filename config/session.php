<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Session Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "file", "cookie", "database", "memcached",
    |            "redis", "dynamodb", "array"
    |
    */

    'driver' => env('SESSION_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime
    |--------------------------------------------------------------------------
    |
    | Number of minutes session stays active. Set expire_on_close=true
    | if you want the session to end when the browser closes.
    |
    */

    'lifetime' => (int) env('SESSION_LIFETIME', 120),

    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

    /*
    |--------------------------------------------------------------------------
    | Session Encryption
    |--------------------------------------------------------------------------
    |
    | Encrypt all session data before storing it.
    |
    */

    'encrypt' => env('SESSION_ENCRYPT', true),

    /*
    |--------------------------------------------------------------------------
    | File Session Location
    |--------------------------------------------------------------------------
    |
    | Location for session files if using "file" driver.
    |
    */

    'files' => storage_path('framework/sessions'),

    /*
    |--------------------------------------------------------------------------
    | Database Session Connection
    |--------------------------------------------------------------------------
    |
    | If using "database" or "redis", specify the connection here.
    |
    */

    'connection' => env('SESSION_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Database Session Table
    |--------------------------------------------------------------------------
    |
    | Table to store sessions (for "database" driver).
    |
    */

    'table' => env('SESSION_TABLE', 'sessions'),

    /*
    |--------------------------------------------------------------------------
    | Cache Store
    |--------------------------------------------------------------------------
    |
    | Define the cache store for session (redis, memcached, etc).
    |
    */

    'store' => env('SESSION_STORE'),

    /*
    |--------------------------------------------------------------------------
    | Session Sweeping Lottery
    |--------------------------------------------------------------------------
    |
    | Probability of cleaning up old sessions (default: 2/100).
    |
    */

    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Name
    |--------------------------------------------------------------------------
    |
    | Default cookie name. You can override via .env (SESSION_COOKIE).
    |
    */

    'cookie' => env('SESSION_COOKIE', 'miftahsso_session'),

    /*
    |--------------------------------------------------------------------------
    | Cookie Path
    |--------------------------------------------------------------------------
    |
    | Path the cookie is available to (default: "/").
    |
    */

    'path' => env('SESSION_PATH', '/'),

    /*
    |--------------------------------------------------------------------------
    | Cookie Domain
    |--------------------------------------------------------------------------
    |
    | Domain where session cookie is valid.
    | Example: ".localhost" for subdomain sharing.
    |
    */

    'domain' => env('SESSION_DOMAIN', null),

    /*
    |--------------------------------------------------------------------------
    | HTTPS Only Cookies
    |--------------------------------------------------------------------------
    |
    | Only send session cookies via HTTPS if true.
    |
    */

    'secure' => env('SESSION_SECURE_COOKIE', false),

    /*
    |--------------------------------------------------------------------------
    | HTTP Access Only
    |--------------------------------------------------------------------------
    |
    | Prevent JavaScript from reading the cookie.
    |
    */

    'http_only' => env('SESSION_HTTP_ONLY', true),

    /*
    |--------------------------------------------------------------------------
    | Same-Site Cookies
    |--------------------------------------------------------------------------
    |
    | "lax" works for most SPAs with Sanctum.
    | Use "none" + secure=true if working across domains.
    |
    */

    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    /*
    |--------------------------------------------------------------------------
    | Partitioned Cookies
    |--------------------------------------------------------------------------
    |
    | Only applies to cross-site contexts. Requires secure + SameSite=none.
    |
    */

    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

];
