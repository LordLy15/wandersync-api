FROM php:8.4-cli AS builder

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

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy application code
COPY . .

# Create storage directories
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Generate autoloader
RUN composer dump-autoload --optimize --no-dev

# ============================================
# Final stage
# ============================================
FROM php:8.4-cli

# Install only runtime dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Install only runtime PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql

# Copy application from builder
COPY --from=builder /var/www/html /var/www/html

WORKDIR /var/www/html

# Expose port
EXPOSE 8080

# Start command - directly use PHP built-in server
CMD ["php", "-S", "0.0.0.0:8080", "router.php"]
