#!/bin/bash

# Use Railway's PORT env var, default to 8080
export PORT="${PORT:-8080}"

echo "Starting WanderSync API on port $PORT"

# Clear all caches
rm -rf bootstrap/cache/*.php
echo "Cleared bootstrap cache"

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Rebuild autoloader
composer dump-autoload --optimize --no-dev 2>&1 | grep -v "Plugins have been disabled" || true
echo "Regenerated autoloader"

# Run package discover
php artisan package:discover --ansi 2>&1 || echo "Package discover skipped"
echo "Package discovery complete"

# Run migrations
php artisan migrate --force 2>/dev/null || true

# Start PHP built-in server with router
exec php -S "0.0.0.0:${PORT}" -t public router.php
