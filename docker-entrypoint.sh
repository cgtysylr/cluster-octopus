#!/bin/sh

set -e

echo "Creating necessary directories..."
mkdir -p storage/framework/views &&
mkdir -p storage/framework/cache &&
chmod -R 775 storage/framework

echo "Waiting for database to be ready..."
sleep 10

echo "Running migrations..."
php artisan migrate --force

echo "Starting Laravel server..."
exec "$@"


