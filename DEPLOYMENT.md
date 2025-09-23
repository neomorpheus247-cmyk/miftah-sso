# Deployment Guide

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