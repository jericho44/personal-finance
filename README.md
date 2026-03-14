# Smart Portal - Personal Finance Management

A comprehensive, robust Personal Finance web application built with Laravel 12 (Backend) and Vue 3 + TypeScript (Frontend). This application enables users to manage accounts, track income and expenses, set financial budgets, monitor goals, get reminders for upcoming bills, and generate insightful financial reports.

## System Architecture

The project leverages a robust modern stack and Clean Architecture principles via the Repository Pattern.

-   **Backend:** Laravel 12 API (PHP 8.2)
-   **Frontend:** Vue 3 (Composition API) + Vue Router + Pinia + TypeScript
-   **Styling:** Custom CSS + Bootstrap (Metronic structure)
-   **Database:** PostgreSQL 14+
-   **Build Tool:** Vite
-   **Environment:** Docker / docker-compose (also supports Laragon/Local server)

## Core Features

-   🔐 **Authentication & Security:** Secure login flow with Token-based auth (Sanctum), Role-Based Access Control (RBAC), password management, and 2FA capability.
-   💰 **Account Management:** Track balances across multiple physical or digital accounts.
-   🏷️ **Category Management:** Categorize income and expenses with customizable colors and icons.
-   💸 **Transaction Tracking:** Record incomes, expenses, and inter-account transfers. Updates account balances seamlessly.
-   📊 **Budgeting:** Set spending limits for specific categories and visually track progress (percentage bars).
-   🎯 **Financial Goals:** Set monetary goals with deadlines and actively track savings progress.
-   📅 **Bills & Subscriptions:** Manage recurring payments, get visual reminders for upcoming due dates, and mark bills as paid.
-   📈 **Reporting & Analytics:** View Monthly and Yearly cash flow summaries, category expense breakdowns via charts (ApexCharts), and export data to Excel files.
-   🌓 **Dark Mode:** Full application support for light and dark themes with persistent preference.

## Getting Started

### Prerequisites

-   [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Recommended for isolated environment)
-   OR Local Environment:
    -   PHP 8.2 (extensions: pdo_pgsql, redis, gd, mbstring, openssl, curl, zip)
    -   Composer v2
    -   Node.js v20 LTS & NPM
    -   PostgreSQL 14+

### Installation (Docker - Recommended)

This project contains specific configurations to run completely within Docker securely.

1. **Clone the repository:**
   ```bash
   git clone git@gitlab.com:yourrepo/smart-portal.git
   cd smart-portal
   ```

2. **Setup Environment Variables:**
   ```bash
   cp .env.docker .env
   # Or configure manually if needed
   ```

3. **Build and start the Docker containers:**
   ```bash
   docker-compose up -d --build
   ```

4. **Install backend dependencies:**
   ```bash
   docker-compose exec app composer install
   ```

5. **Generate API key & Run migrations:**
   ```bash
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate --seed
   ```

6. **Install frontend dependencies & build:**
   ```bash
   docker-compose exec node npm install
   docker-compose exec node npm run build
   ```

The application will be accessible at `http://localhost:8000`.

### Installation (Local / Laragon)

1. **Setup Environment Variables:**
   ```bash
   cp .env.local .env
   ```
   *Edit `.env` to match your local PostgreSQL configuration.*

2. **Install Dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Database Setup:**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```

4. **Run Development Servers:**
   ```bash
   # Terminal 1: Laravel Backend
   php artisan serve

   # Terminal 2: Vue Frontend (Hot module reloading)
   npm run dev
   ```

## Development Workflow

-   **Environment Switching:** You can use the `switch-env.ps1` script to quickly flip `.env` and `vite.config.ts` between `local` and `docker`.
    ```powershell
    # Windows PowerShell
    .\switch-env.ps1 local
    .\switch-env.ps1 docker
    ```

-   **Frontend Changes:** When making changes to Vue components, use `npm run dev` for real-time updates. Run `npm run build` before committing to build production assets.

## Production Deployment

### Pre-deployment Checklist
-   [ ] Set `APP_ENV=production`
-   [ ] Set `APP_DEBUG=false`
-   [ ] Configure production database credentials
-   [ ] Build frontend assets (`npm run build`)
-   [ ] Optimize Laravel:
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

## Licensing

This project is licensed under the MIT License.
