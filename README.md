# Template Laravel API Project Energeek With Frontend VUE (TYPESCRIPT)

A comprehensive Laravel 12 API template featuring authentication, 2FA, role-based access control, Firebase notifications, and Indonesian location data integration. Built with repository pattern architecture and comprehensive security features.

> **Laravel 12 Upgrade**: This template has been upgraded from Laravel 10 to Laravel 12. See [LARAVEL_12_UPGRADE_NOTES.md](LARAVEL_12_UPGRADE_NOTES.md) for complete upgrade details, breaking changes, and compatibility information.

## Features

-   🔐 **Comprehensive Authentication**

    -   Laravel Sanctum token-based authentication
    -   Multi-factor authentication (Google Authenticator + Email OTP)
    -   Email verification
    -   Google OAuth integration via Laravel Socialite

-   🛡️ **Security**

    -   Role-based access control (RBAC)
    -   Custom middleware for XSS protection, throttling, and X-Frame-Options
    -   UUID support for public-facing IDs
    -   Error logging with comprehensive tracking

-   📱 **Firebase Cloud Messaging**

    -   Push notification support via Firebase HTTP v1 API
    -   Notification queue management
    -   User device token management

-   🗂️ **Repository Pattern**

    -   Interface-based architecture - Clean separation of concerns - Dependency injection ready

-   🌏 **Indonesian Location Data**

    -   Complete database of provinces, cities, districts, and villages
    -   API endpoints for location selection
    -   Via laravolt/indonesia package

-   📦 **File & Image Management**

    -   File upload utilities
    -   Image processing with Intervention Image v3
    -   Protected storage endpoints

-   📝 **API Documentation**

    -   Swagger/OpenAPI via L5-Swagger
    -   Auto-generated documentation
    -   Available at `/api/documentation`

-   🧪 **Quality Assurance**

    -   Pest PHP v3.7+ testing framework
    -   PHPUnit v11.5+ (required by Pest 3.7+)
    -   Laravel Pint code formatting

## Table of Contents

-   [Features](#features)
-   [Getting Started](#getting-started)
    -   [Prerequisites](#prerequisites)
    -   [PHP Modules](#php-modules)
    -   [Installation](#installation)
-   [Running Development Server](#running-development-server)
-   [API Testing with Bruno](#api-testing-with-bruno)
-   [Development Workflow](#development-workflow)
-   [Environment Configuration](#environment-configuration)
-   [Testing](#testing)
-   [Production Deployment](#production-deployment)

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

Required software to run this project:

-   [PHP v8.2.x](https://www.php.net/downloads.php) - Server-side programming language (locked to 8.2 for stability)
-   [Composer v2.x](https://getcomposer.org/download/) - PHP dependency manager
-   Minimal version [PostgreSQL 14.19](https://www.postgresql.org/download/) - Primary database
-   [Bruno](https://www.usebruno.com/) - API testing tool (recommended)
-   Minimal version [Node JS v20.19.5 LTS](https://nodejs.org/en/download/package-manager)
-   Minimal version [NPM v10.8.2](https://nodejs.org/en/download/package-manager)
-   [Redis](https://redis.io/download) - Optional but recommended for caching and sessions

### PHP Modules

The following PHP modules are required:

-   bcmath
-   bz2
-   calendar
-   Core
-   ctype
-   curl
-   date
-   dom
-   exif
-   FFI
-   fileinfo
-   filter
-   ftp
-   gd
-   gettext
-   hash
-   iconv
-   igbinary
-   imagick
-   imap
-   intl
-   json
-   ldap
-   libxml
-   mbstring
-   openssl
-   pcntl
-   pcre
-   PDO
-   pdo_pgsql
-   pdo_sqlite
-   pgsql
-   Phar
-   posix
-   random
-   readline
-   redis
-   Reflection
-   session
-   shmop
-   SimpleXML
-   soap
-   sockets
-   sodium
-   SPL
-   sqlite3
-   standard
-   sysvmsg
-   sysvsem
-   sysvshm
-   tokenizer
-   xml
-   xmlreader
-   xmlwriter
-   xsl
-   Zend OPcache
-   zip
-   zlib

**Important PHP Extensions**: Ensure these critical extensions are enabled:

-   `pdo_pgsql` - PostgreSQL database support
-   `redis` - Redis caching support
-   `gd` or `imagick` - Image processing
-   `mbstring` - Multi-byte string support
-   `openssl` - Security and encryption
-   `curl` - HTTP requests
-   `zip` - Archive handling

### Installation

A step-by-step guide on setting up the project locally Laravel with FE Vue.js (typescript)

1. Clone the repository.

via ssh

```bash
git clone -b development-vue git@gitlab.com:energeek/template-laravel-vue-energeek.git
```

via https

```bash
git clone -b development-vue https://gitlab.com/energeek/template-laravel-vue-energeek.git
```

2. Navigate into the directory.

```bash
cd template-api-laravel
```

3. Install the dependencies.

```bash
composer install
```

4. Copy file `.env.example` to `.env` and configure database settings, payment gateway settings, and any other configurations you need.

```bash
cp .env.example .env
```

5. Generate application key using this command.

```bash
php artisan key:generate
```

6. Running migration and seeder for initial data.

```bash
php artisan migrate --seed
```

7. Running migration to initiate data for province, city, district, village for Indonesia.

```bash
php artisan laravolt:indonesia:seed
```

## Running Development Server for Backend (Laravel)

How to run the development server.

1. Start the application using development environment.

```bash
php artisan serve
```

## Running Development Server for Frontend (Vite + Vue)

How to run the development server.

1. Ensure that Node.js is installed, then run the following command to install all required dependencies:

```bash
npm install
```

2. To launch the development server, including the frontend, use the following command:

```bash
npm run dev
```

## Running server Development Backend (Laravel) + Frontend (Vite + Vue) with Hot Reloading

1. To run both the backend and frontend development servers with hot reloading, use the following command:

```bash
composer dev
```

The API will be available at `http://localhost:8000`

### API Documentation

Once the server is running, access the Swagger API documentation at:

```
http://localhost:8000/api/documentation
```

## API Testing with Bruno

### Setting up Bruno

1. Download and install Bruno from [https://www.usebruno.com/](https://www.usebruno.com/)
2. Create a new collection for this project
3. Set the base URL to your Laravel application (e.g., `http://localhost:8000`)

### Environment Configuration

Create environments in Bruno for different stages:

**Local Environment:**

-   Base URL: `http://localhost:8000`
-   API Prefix: `/api`

**Development Environment:**

-   Base URL: `https://dev.yourapp.com`
-   API Prefix: `/api`

### Authentication Setup

1. **Login Request:**

    - Method: POST
    - URL: `{{baseUrl}}/api/auth/login`
    - Body (JSON):

    ```json
    {
        "username": "administrator",
        "password": "your_password"
    }
    ```

2. **Set Authentication Token:**
   After successful login, add the token to your collection's environment variables:

    - Variable: `authToken`
    - Value: `Bearer {{response.data.token.access_token}}`

3. **Use in requests:**
   Add to request headers:
    ```
    Authorization: {{authToken}}
    ```

## Development Workflow

### Code Quality

Run code formatting and testing:

```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Check code format without making changes
./vendor/bin/pint --test

# Run tests
./vendor/bin/pest
# or
./vendor/bin/phpunit
```

### Database Management

```bash
# Create new migration
php artisan make:migration create_example_table

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Seed database
php artisan db:seed

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

### API Development Best Practices

```bash
# Generate API documentation
php artisan l5-swagger:generate

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Create new API controller
php artisan make:controller Api/YourController

# Create new model with migration
php artisan make:model YourModel -m

# Create repository and interface
php artisan make:repository YourRepository
```

## Environment Configuration

### Required Environment Variables

Copy `.env.example` to `.env` and configure:

```bash
# Application
APP_NAME="Your App Name"
APP_ENV=local
APP_KEY=base64:your_app_key_here
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# Redis (for caching and sessions)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls

# Firebase (for push notifications)
FIREBASE_CREDENTIALS=path/to/firebase/credentials.json
```

## Testing

### Running Tests

```bash
# Run all tests
./vendor/bin/pest

# Run specific test file
./vendor/bin/pest tests/Feature/AuthTest.php

# Run tests with coverage
./vendor/bin/pest --coverage

# Run tests in parallel
./vendor/bin/pest --parallel
```

### Test Database

Configure separate test database in `phpunit.xml`:

```xml
<env name="DB_CONNECTION" value="pgsql"/>
<env name="DB_DATABASE" value="your_test_database"/>
```

## Known Issues & Cleanup

### Files to Remove

Before using this template in production, remove or address these files:

1. **Backup files** (delete immediately):

    ```bash
    rm -f .gitlab-ci.yml.backup-*.delete
    ```

2. **Test/Example endpoints** in `routes/api.php`:

    - All `/api/test-*` routes should be removed or gated behind `APP_DEBUG=true`
    - Example controllers in `app/Http/Controllers/Api/Example*.php`
    - `ForceErrorController.php` (testing only)

3. **Stale documentation**:
    - `TODOS.md` - Contains incomplete tasks

### Security Warnings

⚠️ **CRITICAL**: Ensure `.env` file is never committed to version control

-   Check: `git ls-files | grep "\.env$"`
-   If found: `git rm --cached .env`

⚠️ Firebase credentials should be stored securely and never committed

⚠️ Review `DB_PORT=5433` in your `.env` (PostgreSQL default is 5432)

## Troubleshooting

### Common Issues

1. **Permission denied errors:**

    ```bash
    cd {folder_project_name}
    sudo chown -R www-data:www-data storage/ bootstrap/cache/
    sudo chmod 775 -R storage/ bootstrap/cache/
    sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
    sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
    sudo chmod g+s storage bootstrap/cache
    ```

2. **Database connection issues:**

    - Ensure PostgreSQL is running
    - Check database credentials in `.env`
    - Verify database exists

3. **Node.js module issues:**

    ```bash
    cd {folder_project_name}
    rm -rf node_modules/
    npm install
    ```

4. **Composer issues:**

    ```bash
    cd {folder_project_name}
    composer clear-cache
    rm -rf vendor/
    composer install
    ```

## Production Deployment

### Pre-deployment Checklist

-   [ ] Set `APP_ENV=production`
-   [ ] Set `APP_DEBUG=false`
-   [ ] Configure proper database credentials
-   [ ] Set up SSL certificates
-   [ ] Configure web server (Nginx/Apache)
-   [ ] Set up process manager (PM2/Supervisor)
-   [ ] Configure logging and monitoring
-   [ ] Set up backup strategies

### Build Commands FOR PRODUCTION ONLY

```bash
# Install dependencies (production)
composer install --no-dev --optimize-autoloader

# Optimize Laravel after config credential in .env
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Run seeder if needed

# Install Frontend things
npm install
npm run build

# Generate API documentation if needed
php artisan l5-swagger:generate
```

### Web Server Configuration

**Nginx configuration example:**

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/html/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```
