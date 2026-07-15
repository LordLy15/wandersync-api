#!/bin/bash
set -e

# Railway sets PORT env var - use it, default to 8080
PORT=${PORT:-8080}
# Ensure PORT is numeric and not 0
if [ -z "$PORT" ] || [ "$PORT" = "0" ]; then
    PORT=8080
fi

echo "Starting on port $PORT"

# If APP_KEY is not set, generate one
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY not set, generating..."
    php artisan key:generate --force
else
    # Set APP_KEY in .env
    if [ ! -f .env ]; then
        cp .env.example .env
    fi
    # Replace APP_KEY in .env
    sed -i "s/APP_KEY=.*/APP_KEY=$APP_KEY/" .env
fi

# Run migrations
php artisan migrate --force || echo "Migration skipped (may have already run)"

# Start server with router.php for proper Laravel routing
exec php -S 0.0.0.0:$PORT router.php
