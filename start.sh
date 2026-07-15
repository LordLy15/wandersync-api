#!/bin/bash

# Railway passes PORT env var
echo "Railway PORT: $PORT"

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force

# Start server
exec php -S 0.0.0.0:${PORT:-8080} -t public
