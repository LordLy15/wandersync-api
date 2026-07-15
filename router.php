<?php

// Laravel Router for PHP Built-in Server

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Root path
if ($uri === '/') {
    require __DIR__.'/public/index.php';
    return;
}

// API routes
if (strpos($uri, '/api/') === 0) {
    require __DIR__.'/public/index.php';
    return;
}

// Serve static files if they exist
$file = __DIR__.'/public'.$uri;
if (is_file($file)) {
    return false; // Let PHP built-in server handle static files
}

// Default to Laravel index.php
require __DIR__.'/public/index.php';
