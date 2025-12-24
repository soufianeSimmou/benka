#!/bin/bash

# Create database directory if it doesn't exist
mkdir -p database

# Create SQLite database file if it doesn't exist
touch database/database.sqlite

# Set proper permissions
chmod 664 database/database.sqlite
chmod 775 database

# Run migrations
php artisan migrate --force

# Cache configuration, routes and views for production performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Install RoadRunner if not present
if [ ! -f "./rr" ]; then
    vendor/bin/rr get-binary
fi

# Start Octane with RoadRunner (much faster than php artisan serve)
php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=$PORT --rpc-port=6001 --workers=4
