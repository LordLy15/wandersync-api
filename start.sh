#!/bin/bash

# Use Railway's PORT env var, default to 8080
export PORT="${PORT:-8080}"

echo "Starting WanderSync API on port $PORT"

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Clear and regenerate autoloader
composer dump-autoload --optimize --no-dev 2>/dev/null || true

# Run migrations (ignore errors if already run)
php artisan migrate --force 2>/dev/null || true

# Start PHP built-in server with router
exec php -S "0.0.0.0:${PORT}" -t public router.php
