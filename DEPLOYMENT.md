# Deployment Guide

## Project Structure

### Backend (Laravel)
```
miftah-sso/
├── app/                  # Application core code
│   ├── Http/            # Controllers, Middleware, Requests
│   ├── Models/          # Eloquent models
│   ├── Jobs/            # Queue jobs (e.g., ProcessDelayedLogout)
│   └── Policies/        # Authorization policies
├── config/              # Configuration files
├── database/            # Migrations, factories, and seeders
├── resources/           # Views and frontend assets
├── routes/              # API and web routes
└── tests/               # Feature and Unit tests
```

### Frontend (Vue.js)
```
miftah-sso-frontend/
├── src/
│   ├── assets/          # Static assets (CSS, images)
│   ├── components/      # Vue components
│   │   ├── auth/        # Authentication components
│   │   ├── common/      # Shared components
│   │   ├── course/      # Course-related components
│   │   └── layout/      # Layout components
│   ├── composables/     # Vue composables (hooks)
│   ├── router/          # Vue Router configuration
│   ├── stores/          # Pinia stores
│   ├── types/           # TypeScript types/interfaces
│   ├── utils/           # Utility functions
│   └── views/           # Page components
│       ├── admin/       # Admin pages
│       ├── student/     # Student pages
│       └── teacher/     # Teacher pages
```

## Frontend Deployment (Vercel)

1. Push your frontend code to a GitHub repository:
```bash
cd /path/to/miftah-sso-frontend
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/yourusername/miftah-sso-frontend.git
git push -u origin main
```

2. Connect your GitHub repository to Vercel:
   - Go to [Vercel](https://vercel.com)
   - Click "Import Project"
   - Select your repository
   - Configure build settings:
     - Framework Preset: Vite
     - Build Command: `npm run build`
     - Output Directory: `dist`
   - Add Environment Variables:
     - `VITE_API_URL`: Your backend API URL
     - `VITE_APP_NAME`: MiftahSSO

3. Deploy your frontend:
   - Vercel will automatically deploy your frontend
   - Configure your custom domain in Vercel dashboard

## Backend Deployment (Laravel Forge)

1. Prepare your server:
   - Create a new server in Laravel Forge
   - Choose your preferred provider (DigitalOcean, AWS, etc.)
   - Select PHP 8.2
   - Include Redis and MySQL

2. Configure your site:
   - Create a new site in Forge
   - Point it to your domain (api.yourdomain.com)
   - Install SSL certificate

3. Configure environment:
   - Copy `.env.forge` to `.env` on the server
   - Update database credentials
   - Update Redis password
   - Update Google OAuth credentials
   - Update mail settings

4. Configure deployment:
   - Add your GitHub repository to Forge
   - Set up Quick Deploy
   - Add deployment script from `forge-deploy.sh`

5. Set up the database:
```bash
php artisan migrate --force
php artisan db:seed --force
```

6. Configure queue worker:
```bash
# Install supervisor
sudo apt-get install supervisor

# Create worker configuration
sudo nano /etc/supervisor/conf.d/miftah-worker.conf

# Add configuration:
[program:miftah-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/forge/api.yourdomain.com/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=forge
numprocs=2
redirect_stderr=true
stdout_logfile=/home/forge/api.yourdomain.com/worker.log

# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start "miftah-worker:*"
```

7. Configure Nginx:
   - Copy the contents of `nginx.conf` to your site's Nginx configuration in Forge
   - Update the domain names and SSL certificate paths

8. Final steps:
   - Update Google OAuth callback URL in Google Console
   - Update CORS settings in both frontend and backend
   - Test the authentication flow

## Security Checklist

1. Frontend:
   - [ ] Environment variables are properly set
   - [ ] API URL uses HTTPS
   - [ ] CORS is properly configured

2. Backend:
   - [ ] APP_DEBUG is set to false
   - [ ] APP_ENV is set to production
   - [ ] Secure database credentials
   - [ ] Redis password is set
   - [ ] SSL certificate is installed
   - [ ] Session security is configured
   - [ ] CORS is properly configured
   - [ ] Queue worker is running

## Monitoring Setup

1. Set up Laravel Telescope in production (optional):
```bash
composer require laravel/telescope
php artisan telescope:install
```

2. Configure logging:
```bash
# Update .env
LOG_CHANNEL=stack
LOG_LEVEL=error
```

3. Set up error monitoring (recommended):
   - Install and configure Sentry, Bugsnag, or similar service

## Laravel Vapor Deployment

### Prerequisites

1. Install Laravel Vapor CLI:
```bash
composer global require laravel/vapor-cli
```

2. Login to Vapor:
```bash
vapor login
```

3. Initialize Vapor in your project:
```bash
vapor init
```

### Environment Setup

1. Configure Vapor environment variables:
```bash
# Production environment
vapor env:pull production
vapor env:set production \
    APP_NAME="MiftahSSO" \
    APP_ENV=production \
    APP_DEBUG=false \
    GOOGLE_CLIENT_ID=your-production-client-id \
    GOOGLE_CLIENT_SECRET=your-production-client-secret \
    SANCTUM_STATEFUL_DOMAINS=yourdomain.com \
    SESSION_DOMAIN=.yourdomain.com \
    SESSION_SECURE_COOKIE=true

# Staging environment
vapor env:pull staging
vapor env:set staging \
    APP_NAME="MiftahSSO (Staging)" \
    APP_ENV=staging \
    APP_DEBUG=true
```

2. Configure secrets:
```bash
vapor secrets:set production \
    GOOGLE_CLIENT_SECRET=your-production-secret

vapor secrets:set staging \
    GOOGLE_CLIENT_SECRET=your-staging-secret
```

### Database Setup

1. Create databases:
```bash
# Production database
vapor database production

# Staging database
vapor database staging
```

2. Run migrations:
```bash
vapor command production "php artisan migrate --force"
```

### Queue Configuration

1. Configure queue workers:
```bash
# Production workers
vapor queue:deploy production delayed-logout
```

### Cache Configuration

1. Set up cache:
```bash
vapor cache production
```

### Domain Setup

1. Configure domains:
```bash
# Production domain
vapor domain api.yourdomain.com
vapor domain:add api.yourdomain.com

# Staging domain
vapor domain staging.api.yourdomain.com
vapor domain:add staging.api.yourdomain.com
```

2. Configure SSL certificates:
```bash
vapor cert api.yourdomain.com
vapor cert staging.api.yourdomain.com
```

### Deployment

1. Deploy to staging:
```bash
vapor deploy staging
```

2. Deploy to production:
```bash
vapor deploy production
```

### Monitoring

1. View logs:
```bash
# View recent logs
vapor logs production

# View queue worker logs
vapor logs production --worker
```

2. Monitor metrics:
```bash
vapor metrics production
```

### Rollback

If needed, rollback to previous deployment:
```bash
vapor rollback production
```

## Maintenance

1. Regular updates:
```bash
composer update --no-dev
npm update
```

2. Monitor logs:
```bash
tail -f storage/logs/laravel.log
```

3. Monitor queue:
```bash
php artisan queue:monitor
```

4. Database backups:
```bash
php artisan backup:run
```

## Troubleshooting

1. If queue jobs are not processing:
```bash
sudo supervisorctl restart "miftah-worker:*"
```

2. If sessions are not working:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

3. If CORS issues occur:
   - Check Nginx configuration
   - Verify frontend domain in backend CORS config
   - Check SSL certificates

4. If deployment fails:
   - Check Forge deployment logs
   - Check Laravel logs
   - Verify GitHub webhooks