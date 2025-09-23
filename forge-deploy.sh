#!/bin/bash

# Deployment script for Laravel Forge

# Turn on maintenance mode
php artisan down

# Pull the latest changes from the git repository
git pull origin main

# Install/update composer dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Run database migrations
php artisan migrate --force

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Clear and cache config
php artisan config:cache

# Clear and cache routes
php artisan route:cache

# Clear and cache views
php artisan view:cache

# Install/update node modules
# npm ci
# npm run build

# Reload PHP-FPM
sudo systemctl reload php8.2-fpm

# Turn off maintenance mode
php artisan up

# Restart queue worker
php artisan queue:restart