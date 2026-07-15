FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy only essential files first
COPY composer.json composer.lock ./

# Install dependencies without scripts
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy application code
COPY . .

# Create .env file (only non-sensitive defaults, secrets come from Railway env vars)
RUN echo "APP_NAME=Laravel" > .env && \
    echo "APP_ENV=production" >> .env && \
    echo "APP_KEY=" >> .env && \
    echo "APP_DEBUG=false" >> .env && \
    echo "APP_URL=https://wandersync-api.up.railway.app" >> .env && \
    echo "DB_CONNECTION=pgsql" >> .env && \
    echo "SESSION_DRIVER=database" >> .env && \
    echo "QUEUE_CONNECTION=database" >> .env && \
    echo "CACHE_STORE=database"

# Create storage directories
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

# Generate autoloader
RUN composer dump-autoload --optimize --no-dev

# Expose port
EXPOSE 8080

# Start command - generate APP_KEY if not set
CMD ["sh", "-c", "php artisan key:generate --force && php artisan serve --host=0.0.0.0 --port=8080"]
