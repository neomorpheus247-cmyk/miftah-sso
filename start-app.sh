#!/bin/bash

# Get dynamic Replit domain
REPL_DOMAIN="https://${REPLIT_DOMAINS}"

# Set environment variables for Laravel
export APP_KEY="base64:+r1F3hqEyBH+FbV0bwVYuf21RYfS9c8mfC68G1Wg3ww="
export DB_CONNECTION=sqlite
export DB_DATABASE=/tmp/database.sqlite
export APP_ENV=production
export APP_DEBUG=false
export APP_URL="$REPL_DOMAIN"
export ASSET_URL="$REPL_DOMAIN"
export CACHE_STORE=file
export QUEUE_CONNECTION=database

# Google OAuth Configuration (using secure environment variables)
export GOOGLE_REDIRECT_URI="$REPL_DOMAIN/auth/google/callback"

# Session Configuration
export SESSION_DRIVER=cookie
export SESSION_LIFETIME=120
export SESSION_ENCRYPT=true
export SESSION_SECURE_COOKIE=true
export SESSION_PATH=/
export SESSION_DOMAIN="$REPLIT_DOMAINS"
export SESSION_SAME_SITE=none

# Sanctum Configuration
export SANCTUM_STATEFUL_DOMAINS="$REPLIT_DOMAINS"

# Logging Configuration
export LOG_CHANNEL=daily
export LOG_LEVEL=debug

# Vite Configuration
export VITE_API_URL="$REPL_DOMAIN"
export VITE_APP_NAME="MiftahSSO"
export VITE_APP_URL="$REPL_DOMAIN"

# Ensure database exists
touch /tmp/database.sqlite

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Clear and cache config
php artisan config:clear
php artisan config:cache

# Run migrations
php artisan migrate --force

# Build assets first
npm run build

# Clear all caches
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run Laravel server on port 5000
php artisan serve --host=0.0.0.0 --port=5000