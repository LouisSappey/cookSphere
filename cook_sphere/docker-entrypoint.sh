#!/bin/bash
set -e

# Check if the flag file exists
if [ ! -f "/app/.fixtures-loaded" ]; then
    echo "First time setup - running fixtures..."
    /usr/local/bin/docker-fixtures.sh
    # Create flag file to indicate fixtures were loaded
    touch /app/.fixtures-loaded
else
    echo "Fixtures already loaded, skipping..."
    # Just run migrations to ensure schema is up to date
    php bin/console doctrine:migrations:migrate --no-interaction
fi

# Start the PHP server
exec php -S 0.0.0.0:8000 -t public 