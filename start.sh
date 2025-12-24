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

# Start server - simple and fast, no caching
php artisan serve --host=0.0.0.0 --port=$PORT
