#!/bin/sh

set -e
echo "Starting entrypoint script..."


echo "Clearing cache..."
php artisan config:clear

#echo "Creating necessary directories..."
#mkdir -p storage/framework/views &&
#mkdir -p storage/framework/cache &&
#chmod -R 775 storage/framework

echo "Waiting for database to be ready..."
sleep 5

echo "Running migrations..."
php artisan migrate --force

echo "Starting Laravel server..."
exec "$@"


