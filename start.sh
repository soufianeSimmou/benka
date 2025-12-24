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

# Cache everything for maximum performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Start server with OPcache enabled
php -d opcache.enable=1 -d opcache.enable_cli=1 -d opcache.memory_consumption=256 -d opcache.max_accelerated_files=20000 artisan serve --host=0.0.0.0 --port=$PORT
