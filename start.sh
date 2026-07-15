#!/bin/bash

# Use Railway's PORT env var, default to 8080
PORT=${PORT:-8080}

echo "Starting on port $PORT"

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force || true

# Start PHP built-in server
exec php -S 0.0.0.0:$PORT router.php
