#!/bin/bash

# Railway passes PORT env var - use it only if it's valid (not 0 or empty)
if [ -n "$PORT" ] && [ "$PORT" != "0" ]; then
    USE_PORT=$PORT
else
    USE_PORT=8080
fi

echo "Starting on port $USE_PORT"

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force

# Start server with router
exec php -S 0.0.0.0:$USE_PORT router.php
