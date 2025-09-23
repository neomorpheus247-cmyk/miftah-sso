#!/bin/bash

# Exit on error
set -e

# 1. Load environment variables
if [ -f .env ]; then
  echo "Using .env file."
else
  echo "No .env file found!"
  exit 1
fi

# 2. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 3. Install Node.js dependencies and build assets
npm install --legacy-peer-deps
npm run build

# 4. Cache config, routes, and views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Run database migrations
php artisan migrate --force

# 6. Set correct permissions
chmod -R ug+w storage bootstrap/cache

# 7. Start queue worker (background)
nohup php artisan queue:work --queue=delayed-logout > storage/logs/queue.log 2>&1 &

# 8. Print success message
echo "Deployment complete!"
