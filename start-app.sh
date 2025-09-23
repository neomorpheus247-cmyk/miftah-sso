#!/bin/bash

# Set environment variables for Laravel
export APP_KEY="base64:+r1F3hqEyBH+FbV0bwVYuf21RYfS9c8mfC68G1Wg3ww="
export DB_CONNECTION=sqlite
export DB_DATABASE=/tmp/database.sqlite
export APP_ENV=local
export APP_DEBUG=true
export APP_URL=http://localhost:5000
export CACHE_STORE=file
export SESSION_DRIVER=file
export QUEUE_CONNECTION=database
export SESSION_SECURE_COOKIE=false
export SESSION_DOMAIN=null

# Ensure database exists
touch /tmp/database.sqlite

# Build assets first
npm run build

# Run Laravel server on port 5000
php artisan serve --host=0.0.0.0 --port=5000