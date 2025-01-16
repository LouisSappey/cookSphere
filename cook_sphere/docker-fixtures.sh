#!/bin/bash

set -e  # Exit on any error

echo "Running database migrations..."
php bin/console doctrine:database:create --if-not-exists --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction

echo "Loading fixtures..."
php bin/console doctrine:fixtures:load --no-interaction || echo "Fixtures already loaded or not necessary."

# Execute the original command (starting the server)
exec "$@"