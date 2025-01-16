FROM php:8.2-cli

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

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker-fixtures.sh /usr/local/bin/docker-fixtures.sh
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-fixtures.sh /usr/local/bin/docker-entrypoint.sh

RUN useradd -m appuser
USER appuser

WORKDIR /app

COPY --chown=appuser:appuser . /app

RUN composer install --no-interaction --optimize-autoloader --no-scripts

RUN composer require symfony/debug-bundle --dev --no-scripts
RUN composer require debug --no-scripts

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
