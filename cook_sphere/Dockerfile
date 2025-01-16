# Use the official PHP image
FROM php:8.2-cli

# Install system dependencies
RUN apt-get update -y && apt-get install -y \
    libmcrypt-dev \
    libzip-dev \
    unzip \
    git \
    libicu-dev \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip intl mbstring xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create a non-root user
RUN useradd -m appuser
USER appuser

# Set the working directory
WORKDIR /app

# Copy application code
COPY --chown=appuser:appuser . /app

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-scripts

# Run additional composer commands if needed
RUN composer require symfony/debug-bundle --dev --no-scripts
RUN composer require debug --no-scripts

# Expose the application port
EXPOSE 8000

# Start the PHP built-in web server
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
