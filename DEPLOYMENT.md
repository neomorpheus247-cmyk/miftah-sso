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

## Google Cloud Platform Deployment

### Option 1: Google App Engine

1. Install Google Cloud SDK and initialize:
```bash
# Install Google Cloud SDK
curl https://sdk.cloud.google.com | bash
gcloud init
```

2. Create a new project or select existing:
```bash
gcloud projects create miftah-sso-prod
gcloud config set project miftah-sso-prod
```

3. Enable required APIs:
```bash
gcloud services enable \
  appengine.googleapis.com \
  cloudbuild.googleapis.com \
  cloudscheduler.googleapis.com \
  cloudsql.googleapis.com \
  redis.googleapis.com
```

4. Create Cloud SQL instance:
```bash
gcloud sql instances create miftah-sso \
  --database-version=MYSQL_8_0 \
  --tier=db-f1-micro \
  --region=your-region
```

5. Create Redis instance:
```bash
gcloud redis instances create miftah-cache \
  --size=1 \
  --region=your-region \
  --redis-version=redis_6_x
```

6. Update app.yaml with your configuration:
   - Replace YOUR_PROJECT_ID
   - Replace YOUR_REGION
   - Replace YOUR_INSTANCE

7. Deploy:
```bash
gcloud app deploy
```

### Option 2: Cloud Run

1. Enable required APIs:
```bash
gcloud services enable \
  run.googleapis.com \
  cloudbuild.googleapis.com \
  secretmanager.googleapis.com
```

2. Build and deploy:
```bash
# Build the container
gcloud builds submit --tag gcr.io/YOUR_PROJECT_ID/miftah-sso

# Deploy to Cloud Run
gcloud run deploy miftah-sso \
  --image gcr.io/YOUR_PROJECT_ID/miftah-sso \
  --platform managed \
  --region your-region \
  --allow-unauthenticated \
  --set-env-vars="APP_KEY=${APP_KEY},DB_PASSWORD=${DB_PASSWORD}"
```

3. Set up Cloud SQL connection:
```bash
gcloud run services update miftah-sso \
  --add-cloudsql-instances=YOUR_PROJECT_ID:YOUR_REGION:YOUR_INSTANCE
```

### Environment Configuration

1. Store secrets in Secret Manager:
```bash
# Create secrets
echo -n "your-app-key" | gcloud secrets create app-key --data-file=-
echo -n "your-db-password" | gcloud secrets create db-password --data-file=-
echo -n "your-redis-password" | gcloud secrets create redis-password --data-file=-

# Grant access to the service account
gcloud secrets add-iam-policy-binding app-key \
  --member="serviceAccount:YOUR_PROJECT_ID@appspot.gserviceaccount.com" \
  --role="roles/secretmanager.secretAccessor"
```

2. Set up Cloud Storage for media files:
```bash
gsutil mb gs://miftah-sso-media
gsutil iam ch allUsers:objectViewer gs://miftah-sso-media
```

### Monitoring and Logging

1. Set up Cloud Monitoring:
```bash
# Enable Error Reporting
gcloud services enable clouderrorreporting.googleapis.com

# Set up custom metrics
gcloud monitoring metrics-descriptors create \
  custom.googleapis.com/miftah/active_users \
  --description="Number of active users"
```

2. Set up Cloud Logging:
```bash
# Create log sink
gcloud logging sinks create miftah-error-logs \
  storage.googleapis.com/miftah-logs \
  --log-filter="severity>=ERROR"
```

### Security Configuration

1. Set up Cloud Armor:
```bash
# Create security policy
gcloud compute security-policies create miftah-security-policy \
  --description="Security policy for Miftah SSO"

# Add rules
gcloud compute security-policies rules create 1000 \
  --security-policy=miftah-security-policy \
  --expression="request.headers['x-forwarded-for'] != ''" \
  --action=allow
```

2. Configure IAM roles:
```bash
# Create custom role
gcloud iam roles create miftahApp \
  --project=YOUR_PROJECT_ID \
  --title="Miftah App Role" \
  --description="Custom role for Miftah SSO application" \
  --permissions=cloudsql.instances.connect,redis.instances.connect
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