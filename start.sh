#!/bin/bash
set -e

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

# Start server
exec php -S 0.0.0.0:8080 -t public
