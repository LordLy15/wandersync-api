<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supabase Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for connecting to Supabase services including database,
    | authentication, and storage.
    |
    */

    'url' => env('SUPABASE_URL'),

    'anon_key' => env('SUPABASE_ANON_KEY'),

    'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Supabase Database Connection
    |--------------------------------------------------------------------------
    |
    | The database connection uses the PostgreSQL driver configured in
    | config/database.php. Connection details are in .env file.
    |
    */

    'database' => [
        'host' => env('DB_HOST'),
        'port' => env('DB_PORT', 5432),
        'database' => env('DB_DATABASE', 'postgres'),
        'username' => env('DB_USERNAME', 'postgres'),
        'password' => env('DB_PASSWORD'),
    ],
];
