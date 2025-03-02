#!/bin/sh

set -e
echo "Starting entrypoint script..."

echo "Clearing cache..."
php artisan config:clear

echo "Waiting for database to be ready..."
sleep 5

echo "Running migrations..."
php artisan migrate --force

echo "Running Scheduled Tasks ..."
php artisan schedule:work &

echo "Starting Laravel server..."
exec "$@"
