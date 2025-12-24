#!/bin/bash

# Run migrations
php artisan migrate --force

# Cache configuration, routes and views for production performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize --classmap-authoritative

# Start server
php artisan serve --host=0.0.0.0 --port=$PORT
