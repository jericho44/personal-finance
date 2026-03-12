# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is an API-first Laravel 12 template project designed for building modern web applications with comprehensive authentication, authorization, and notification features. The project follows repository pattern architecture with interface-based dependency injection.

**Note:** This project was recently upgraded from Laravel 10 to Laravel 12. See `LARAVEL_12_UPGRADE_NOTES.md` for complete upgrade details, breaking changes, and compatibility information.

## Development Commands

### Backend (Laravel)
- `php artisan serve` - Start Laravel development server (default: http://localhost:8000)
- `php artisan migrate --seed` - Run migrations and seeders
- `php artisan laravolt:indonesia:seed` - Seed Indonesian location data (provinces, cities, districts, villages)
- `php artisan key:generate` - Generate application key
- `composer install` - Install PHP dependencies
- `./vendor/bin/pest` - Run tests using Pest
- `./vendor/bin/phpunit` - Run tests using PHPUnit
- `./vendor/bin/pint` - Run Laravel Pint for code formatting

### Frontend
**Note**: This template currently has no frontend build system configured. You can integrate:
- Vue 3 + Vite (recommended for SPAs)
- React + Vite
- Inertia.js (for monolithic apps)
- Or consume the API from a separate frontend application

## Architecture Overview

This is a Laravel 12 API-first application with the following architectural patterns:

### Controller Organization
- **API Controllers** (`app/Http/Controllers/Api/`) - JSON API endpoints
- **ApiWeb Controllers** (`app/Http/Controllers/ApiWeb/`) - Web-based API endpoints
- **Auth Controllers** (`app/Http/Controllers/Auth/`) - Authentication handling

### Repository Pattern
- All repositories implement interfaces in `app/Interfaces/`
- Base repository (`app/Repositories/BaseRepository.php`) provides common CRUD operations
- Repositories are bound to interfaces via `RepositoryServiceProvider`
- Controllers depend on interfaces, not concrete implementations

### Key Architectural Components

#### Data Access Layer
- Repository pattern with interface contracts
- BaseRepository for common operations (findAll, findById, create, update, delete)
- Service provider registration for dependency injection

#### Response Handling
- `ResponseFormatter` helper for consistent API responses
- Standardized error handling with context logging
- JSON responses for API endpoints

#### Security & Authentication
- Multi-guard authentication (Sanctum for API, sessions for web)
- Role-based access control with `RoleAuthorization` middleware
- Firebase integration for push notifications
- Google OAuth via Laravel Socialite

#### Model Architecture
- UUID strategy using `id_hash` field (keeps integer PKs internally)
- `HasUUID` trait for automatic UUID generation
- `ErrorLogging` trait for comprehensive error tracking
- Custom enum system with reflection-based functionality

#### Frontend Integration
- **Currently no frontend build system configured**
- Blade templates available for email notifications (`resources/views/`)
- API endpoints ready for consumption by any frontend framework
- Recommended: Vue 3 + Vite or React + Vite for SPA development
- Alternative: Inertia.js for server-rendered SPAs

### Important Files & Patterns

#### Helper Classes
- `app/Helpers/ResponseFormatter.php` - Standardized API responses
- `app/Helpers/FileUpload.php` - File upload utilities
- `app/Helpers/Firebase.php` - Firebase integration
- `app/Helpers/helper.php` - Global helper functions

#### Middleware
- `RoleAuthorization` - Role-based access control
- `CustomAuthenticateWithBasicAuth` - Basic auth with role checking
- `CustomThrottleRequests` - Rate limiting
- `HTMLEntitiesConverter` - Security middleware
- `Programic\LaravelConvertCaseMiddleware` - API request/response case conversion (camelCase ↔ snake_case)

#### Testing
- Uses Pest PHP testing framework
- Tests located in `tests/Feature/` and `tests/Unit/`
- PHPUnit configuration in `phpunit.xml`

#### Code Formatting
- Laravel Pint for code formatting

### Database
- PostgreSQL as primary database
- Indonesian location data via `laravolt/indonesia` package
- Soft deletes with cascade support
- User stamps tracking (created_by, updated_by)

### API Documentation
- Swagger/OpenAPI documentation via `darkaonline/l5-swagger`
- Documentation available at `/api/documentation`

### Development Environment
- PHP 8.2.* required (locked for stability)
- Laravel 12.x
- PostgreSQL 14.13+ for database
- Redis for caching and sessions (optional but recommended)
- Composer 2.x for dependency management

## Authentication & Security

### Multi-Factor Authentication (2FA)
The application implements comprehensive 2FA using two methods:
1. **Google Authenticator** - Time-based OTP (TOTP) via `pragmarx/google2fa-laravel`
2. **Email OTP** - Custom OTP system with expiration and verification

Flow diagram available in: `flow_auth_2fa.drawio`

### Authentication Guards
- **api** - Sanctum token-based authentication for API endpoints
- **web** - Session-based authentication (Laravel Breeze scaffolding included but may be unused)

### Key Security Features
- Role-based access control via `RoleAuthorization` middleware
- Custom throttling with `CustomThrottleRequests`
- HTML entity conversion for XSS protection via `HTMLEntitiesConverter`
- X-Frame-Options header via `XFrameOptions` middleware
- Basic authentication support via `CustomAuthenticateWithBasicAuth`

### Protected Routes
Most API endpoints require `auth:sanctum` middleware. Some endpoints additionally require specific roles:
- `role:developer` - Developer-only endpoints (app version, province management)
- `role:developer,admin` - Admin and developer access

## API Endpoints

### Public Endpoints
- `POST /api/auth/login` - User login (rate limited: 10/min)
- `POST /api/auth/register` - User registration (rate limited: 10/min)
- `GET /api/auth/email/verify/{id}/{hash}` - Email verification
- `POST /api/auth/2fa/verify` - Two-factor authentication verification
- `POST /api/auth/2fa/enable` - Enable 2FA for user
- `POST /api/auth/2fa/disable` - Disable 2FA

### Protected Endpoints (require auth:sanctum)
- **Notifications**: CRUD operations for user notifications
- **App Version**: Version management (role:developer)
- **Provinces**: Indonesian location data (role:developer,admin)
- **Select Lists**: Dropdown data for users, roles, countries, provinces, cities, districts, villages
- **Storage**: File access endpoints

### Test/Example Endpoints (⚠️ Remove before production)
The following endpoints are for testing/demonstration only:
- `/api/test-error-*` (500, 502, 503, 504, 400, 401, 403, 404, 422, 429) - Error testing
- `/api/test-file-upload-single` & `/api/test-file-upload-multiple` - File upload examples
- `/api/test-swagger-auto-generate` - Swagger documentation generation
- `/api/test-enum-list` - Enum usage example
- `/api/test-send-firebase` - Firebase notification example

**Action Required**: Gate these behind `APP_DEBUG=true` or remove entirely before deploying to production.

### Example Controllers (⚠️ Template examples)
These controllers demonstrate framework features:
- `ExampleEnumUsageController` - Custom enum system usage
- `ExampleFileUploadController` - File upload patterns
- `ExampleImageController` - Image processing with Intervention
- `ExampleFirebaseHttpV1Controller` - Firebase HTTP v1 API integration

**Recommendation**: Document these in a separate `EXAMPLES.md` file or move to `docs/examples/` directory.

## Firebase Push Notifications

The application includes comprehensive Firebase Cloud Messaging (FCM) integration:

### Configuration
- `config/firebase.php` - Firebase configuration
- Firebase credentials should be stored in path specified by `FIREBASE_CREDENTIALS` env variable
- Uses Firebase HTTP v1 API (modern approach)

### Components
- `app/Helpers/Firebase.php` - Firebase messaging helper
- `app/Models/UserFirebase.php` - User FCM token storage
- `app/Models/FirebasePendingNotification.php` - Queue for pending notifications
- `app/Notifications/FirebaseChannel.php` - Custom notification channel

### Usage
See `ExampleFirebaseHttpV1Controller` for implementation examples.

## File Upload & Storage

### File Upload Helper
`app/Helpers/FileUpload.php` provides utilities for:
- Single/multiple file uploads
- File validation
- Storage path management
- File deletion

### Image Processing
`app/Helpers/Image.php` uses Intervention Image v3 for:
- Image resizing
- Format conversion
- Thumbnail generation
- Optimization

### Storage Access
- Public files: `storage/app/public/` (symlinked to `public/storage`)
- Protected files: `GET /api/storage/{path}` endpoint with authentication

## Indonesian Location Data

Uses `laravolt/indonesia` package for comprehensive Indonesian location database:
- **Provinces** (Provinsi)
- **Cities/Regencies** (Kota/Kabupaten)
- **Districts** (Kecamatan)
- **Villages** (Kelurahan/Desa)

Seed data using: `php artisan laravolt:indonesia:seed`

API endpoints available at:
- `GET /api/select/provinces`
- `GET /api/select/cities/{province_id}`
- `GET /api/select/districts/{city_id}`
- `GET /api/select/villages/{district_id}`

## Code Quality Tools

### Testing
- **Framework**: Pest PHP v3.7+ (modern testing framework)
- **PHPUnit**: v11.5+ (required by Pest 3.7+)
- **Location**: `tests/Feature/` and `tests/Unit/`
- **Configuration**: `phpunit.xml`
- **Run**: `./vendor/bin/pest` or `./vendor/bin/phpunit`

### Code Formatting
- **Tool**: Laravel Pint (opinionated PHP formatter)
- **Run**: `./vendor/bin/pint`
- **Check without changes**: `./vendor/bin/pint --test`

## Important Notes

### Laravel 12 Upgrade
This project was upgraded from Laravel 10 to Laravel 12. Key changes:
- **PHP Version**: Locked to 8.2.* for production stability
- **Package Replaced**: `tomlerendu/laravel-convert-case-middleware` → `programic/laravel-convert-case-middleware` (Laravel 12 support)
- **PHPUnit**: Upgraded to v11.5 (required by Pest 3.7+)
- **Breaking Changes**: Carbon 3.x required, UUID v7 default
- **Pending Testing**: Some packages require manual testing (see `LARAVEL_12_UPGRADE_NOTES.md`)

For complete upgrade details, see `LARAVEL_12_UPGRADE_NOTES.md`.

### Files to Clean Up
The following files should be removed or moved:
1. **Backup files** (marked for deletion):
   - `.gitlab-ci.yml.backup-09102025.delete`
   - `.gitlab-ci.yml.backup-10102025.delete`

2. **Stale documentation**:
   - `TODOS.md` - Contains incomplete/outdated tasks

3. **Test endpoints** - See "Test/Example Endpoints" section above

### Configuration Warnings
- ⚠️ `.env` file may be tracked in git - ensure it's properly ignored
- ⚠️ `DB_PORT=5433` in .env is non-standard (PostgreSQL default is 5432)
- ⚠️ Firebase credentials should NEVER be committed to version control

### Production Deployment Checklist
Before deploying to production:
- [ ] Remove or gate all test/example endpoints
- [ ] Ensure `.env` is not tracked in git
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Enable Redis for cache and sessions
- [ ] Configure proper logging and monitoring
- [ ] Run `composer install --no-dev --optimize-autoloader`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Ensure all tests pass: `./vendor/bin/pest`
- [ ] Format code: `./vendor/bin/pint`
- [ ] Review `LARAVEL_12_UPGRADE_NOTES.md` for pending manual tests