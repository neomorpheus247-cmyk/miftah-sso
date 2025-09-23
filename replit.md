# MiftahSSO - Classroom Single Sign-On System

## Overview

MiftahSSO is a classroom management system built with Laravel 12.0 as the backend API and Vue.js 3 as the frontend SPA. The system provides secure authentication through Google Single Sign-On (SSO) with Role-Based Access Control (RBAC) supporting student, teacher, and admin roles. It features smart session management with delayed logout functionality and a clean separation between backend and frontend through a Backend-for-Frontend (BFF) architecture pattern.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Backend Architecture
The backend is built on Laravel 12.0 following RESTful API principles with a clear separation of concerns:

- **Authentication Layer**: Utilizes Laravel Sanctum for session-based authentication combined with Laravel Socialite for Google OAuth integration. JWT tokens are also supported through tymon/jwt-auth for enhanced security.
- **Authorization System**: Implements Spatie Laravel-Permission package for comprehensive RBAC, supporting multiple roles (student, teacher, admin) with granular permission management.
- **Database Layer**: Uses SQLite for development and MySQL for production environments with Doctrine DBAL for enhanced database abstraction and schema management.
- **Queue System**: Employs Laravel's database-driven queue system for handling background tasks, particularly the ProcessDelayedLogout job for smart session management.
- **Session Management**: Features a sophisticated delayed logout system that provides users with warnings before automatic session termination, enhancing user experience while maintaining security.

### Frontend Architecture
The frontend is built as a Single Page Application (SPA) using Vue.js 3 with modern development practices:

- **Component Architecture**: Uses Vue 3 Composition API with organized component structure (auth, common, course, layout components).
- **State Management**: Implements Pinia for centralized state management, handling authentication state and course data.
- **Routing**: Uses Vue Router with role-based route protection and dynamic imports for code splitting.
- **Type Safety**: Includes TypeScript support for better development experience and code reliability.
- **Build System**: Utilizes Vite for fast development builds and optimized production bundles with manual chunk splitting for vendor libraries.

### API Structure
The system follows RESTful conventions with clear endpoint organization:

- Authentication endpoints handle Google OAuth flow and session management
- Course management endpoints support CRUD operations
- User management endpoints handle role assignments and profile data
- All endpoints return consistent JSON responses with proper HTTP status codes

### Security Implementation
Security is implemented through multiple layers:

- **CORS Configuration**: Uses fruitcake/php-cors for secure cross-origin requests
- **Session Security**: Backend-for-Frontend pattern ensures sensitive operations remain server-side
- **Token Management**: Dual authentication system with Sanctum sessions and JWT tokens for flexibility
- **Input Validation**: Comprehensive validation using Laravel's validation system and custom request classes

### Development Workflow
The system is designed for modern development practices:

- **Asset Compilation**: Laravel Vite plugin handles asset compilation with HMR support
- **Code Quality**: Configured with Laravel Pint for code formatting
- **Testing Structure**: Organized test suite with Feature and Unit tests
- **Development Server**: Configured for local development with proper CORS handling

## External Dependencies

### Authentication Services
- **Google OAuth 2.0**: Primary authentication provider requiring client credentials configuration
- **Laravel Sanctum**: Handles session-based authentication for SPA
- **Laravel Socialite**: Manages OAuth provider integrations

### Database Systems
- **SQLite**: Used in development environment for simplicity
- **MySQL**: Production database system for scalability and performance

### Third-Party Packages
- **Spatie Laravel-Permission**: Comprehensive role and permission management
- **Doctrine DBAL**: Database abstraction layer for advanced schema operations
- **tymon/jwt-auth**: JWT token authentication support
- **fruitcake/php-cors**: Cross-Origin Resource Sharing handling

### Frontend Dependencies
- **Vue.js 3**: Core frontend framework with Composition API
- **Pinia**: State management solution for Vue 3
- **Vue Router**: Client-side routing
- **Axios**: HTTP client for API communication
- **TailwindCSS**: Utility-first CSS framework for styling

### Build and Development Tools
- **Vite**: Build tool and development server
- **Laravel Vite Plugin**: Integration between Laravel and Vite
- **Tailwind CSS Vite Plugin**: TailwindCSS integration with Vite

### Deployment Infrastructure
- **Vercel**: Frontend deployment platform (as indicated in deployment documentation)
- **Laravel Cloud**: Backend deployment platform (referenced in axios base URL configuration)
- **Replit**: Development and hosting platform with integrated database and deployment capabilities

## Replit Environment Setup

### Development Configuration
The project is configured to run in the Replit environment with the following setup:

- **Database**: Uses SQLite for development stored at `/tmp/database.sqlite`
- **Environment**: File-based cache and sessions to avoid Redis dependency
- **Port Configuration**: Laravel server runs on port 5000 with Vite configured for proxy support
- **Asset Building**: Vite builds assets during startup for optimized performance

### Workflow Configuration
The application uses a single workflow (`Laravel App`) that:
1. Builds frontend assets using Vite
2. Runs database migrations automatically
3. Starts Laravel development server on port 5000
4. Configured for web preview with proper host allowance for Replit proxy

### Environment Variables
Key environment variables set for Replit operation:
- `APP_ENV=local` for development mode
- `DB_CONNECTION=sqlite` using local database file
- `CACHE_STORE=file` and `SESSION_DRIVER=file` for file-based storage
- `APP_DEBUG=true` for development debugging
- Host configuration allows Replit proxy to properly serve the application