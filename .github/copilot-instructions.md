# MiftahSSO Copilot Instructions

## Project Overview
MiftahSSO is a classroom management system built with Laravel 12, featuring Google SSO, RBAC, and smart session management. It uses Vue.js 3 for the frontend with Vite as the build tool.

## Key Architecture Patterns

### Authentication Flow
- Google OAuth integration via Laravel Socialite (`app/Http/Controllers/Auth/GoogleController.php`)
- JWT token-based authentication with delayed logout mechanism
- Session management through Laravel Sanctum

### Authorization System
- Role-Based Access Control (RBAC) using spatie/laravel-permission
- Three main roles: admin, teacher, student
- Permissions are defined in database migrations and seeded via `RolesAndPermissionsSeeder`
- Example: See `CoursePolicy.php` for authorization patterns

### Queue System
- Uses Laravel Queue with database driver
- Key job: `ProcessDelayedLogout` for handling smart session termination
- Queue configuration in `.env`: `QUEUE_CONNECTION=database`

## Development Workflow

### Environment Setup
```bash
composer install
npm install --legacy-peer-deps
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
```

### Development Commands
- Start development servers: `composer run dev`
  - Runs Laravel server, queue worker, log viewer, and Vite
- Run tests: `composer run test`
  - Uses in-memory SQLite database
  - PHPUnit configuration in `phpunit.xml`

### Testing Conventions
- Feature tests extend `Tests\Feature\TestCase`
- Database is refreshed between tests using `RefreshDatabase`
- Authentication/authorization tests use `Sanctum::actingAs()`
- Example test patterns in `tests/Feature/CourseApiTest.php`

## Common Patterns

### Model Relationships
- Use type-hinted relationship methods
- Example in `User.php`: `public function courses(): BelongsToMany`

### API Response Format
- Return JSON responses with consistent structure
- Use resource classes for data transformation

### Frontend Architecture
- Vue 3 with Composition API
- State management via Pinia
- Vue Router for SPA navigation