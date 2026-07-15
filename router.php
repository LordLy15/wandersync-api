<?php

/**
 * Laravel Router for PHP Built-in Server
 *
 * Place this file in the project root and use:
 * php -S 0.0.0.0:8080 router.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

// Serve index.php for all other requests
require __DIR__.'/public/index.php';
