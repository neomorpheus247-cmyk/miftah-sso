# MiftahSSO - Classroom Single Sign-On System

A secure classroom management system with Google Single Sign-On, Role-Based Access Control (RBAC), and smart session management.

## Features

- 🔐 Google Single Sign-On (SSO)
- 👥 Role-Based Access Control (RBAC)
- 🔒 Backend-for-Frontend Session Management
- ⏰ Smart Delayed Logout System

## Technology Stack

- **Framework**: Laravel 12.0
- **Authentication**: Laravel Sanctum + Socialite
- **Authorization**: Spatie Laravel-Permission
- **Database**: SQLite (Development) / MySQL (Production)
- **Queue System**: Laravel Queue (Database Driver)
- **Frontend Build**: Vite

## Requirements

- PHP >= 8.2
- Node.js >= 16
- Composer
- SQLite (Development) / MySQL (Production)
- Google OAuth 2.0 Credentials

## Installation

1. Clone the repository:
```bash
git clone https://github.com/neomorpheus247-cmyk/miftah-sso.git
cd miftah-sso
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node.js dependencies:
```bash
npm install --legacy-peer-deps
```

4. Create and configure environment file:
```bash
cp .env.example .env
```

5. Configure the following in your `.env`:
```env
APP_NAME=MiftahSSO
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

6. Generate application key:
```bash
php artisan key:generate
```

7. Create SQLite database:
```bash
touch database/database.sqlite
```

8. Run migrations and seeders:
```bash
php artisan migrate:fresh --seed
```

9. Build frontend assets:
```bash
npm run build
```

10. Start the queue worker:
```bash
php artisan queue:work
```

## Architecture

### Authentication Flow

1. User clicks "Login with Google"
2. User is redirected to Google OAuth consent screen
3. After consent, user is redirected back with OAuth token
4. System creates/updates user record and assigns default role
5. User is authenticated and session is created

### Role-Based Access Control (RBAC)

#### Roles
- **Admin**: Full system access
- **Teacher**: Course management and student view access
- **Student**: Course viewing and enrollment access

#### Permissions
- Course Management:
  - view courses
  - create courses
  - edit courses
  - delete courses
  - enroll in courses
- User Management:
  - manage users
  - view users
- System:
  - manage roles
  - access settings

### Backend-for-Frontend Session

- Uses Laravel Sanctum for API authentication
- Stateful authentication for SPA
- CSRF protection enabled
- Session encryption enabled
- Configurable session lifetime

### Delayed Logout System

Implements a 5-minute delayed logout feature:

1. User initiates delayed logout
2. System queues a logout job for 5 minutes later
3. User can cancel logout within the 5-minute window
4. After 5 minutes, user is automatically logged out

#### Queue Configuration
```env
QUEUE_CONNECTION=database
QUEUE_DELAYED_LOGOUT_CONNECTION=database
QUEUE_DELAYED_LOGOUT_QUEUE=delayed-logout
QUEUE_DELAYED_LOGOUT_ATTEMPTS=3
QUEUE_DELAYED_LOGOUT_BACKOFF=60
```

## API Routes

### Authentication
```
GET  /auth/google              - Redirect to Google OAuth
GET  /auth/google/callback     - Handle OAuth callback
POST /auth/logout              - Immediate logout
POST /auth/logout/schedule     - Schedule delayed logout
POST /auth/logout/cancel       - Cancel scheduled logout
```

### Courses
```
GET    /api/courses           - List all courses
POST   /api/courses          - Create new course
GET    /api/courses/{id}     - Get course details
PUT    /api/courses/{id}     - Update course
DELETE /api/courses/{id}     - Delete course
```

## Security Features

1. **OAuth Security**
   - State verification
   - HTTPS enforced in production
   - Secure token handling

2. **RBAC Security**
   - Server-side role verification
   - Policy-based authorization
   - Route middleware protection

3. **Session Security**
   - Encrypted sessions
   - CSRF protection
   - Secure cookie settings
   - Token-based API authentication

4. **Logout Security**
   - Immediate logout option
   - Cancelable delayed logout
   - Queue job monitoring
   - Token invalidation

## Development

### Running Tests
```bash
php artisan test
```

### Starting Development Server
```bash
php artisan serve
```

### Watching Assets
```bash
npm run dev
```

### Queue Worker (Development)
```bash
php artisan queue:work --queue=delayed-logout
```

## Production Deployment

1. Set production environment variables:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

2. Configure secure session settings:
```env
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.your-domain.com
```

3. Install dependencies:
```bash
composer install --no-dev --optimize-autoloader
```

4. Build assets:
```bash
npm run build
```

5. Cache configuration:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

6. Set up Laravel worker service:
```bash
sudo cp laravel-worker.service /etc/systemd/system/
sudo systemctl enable laravel-worker.service
sudo systemctl start laravel-worker.service
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Contributing

Please read CONTRIBUTING.md for details on our code of conduct and the process for submitting pull requests.
