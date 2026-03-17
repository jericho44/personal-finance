# Smart Portal - Personal Finance Management

A comprehensive, robust Personal Finance web application built with **Laravel 12 (Backend)** and **Vue 3 + TypeScript (Frontend)**. This application enables users to manage accounts, track income and expenses, set financial budgets, monitor goals, get reminders for upcoming bills, and generate insightful financial reports.

## System Architecture

The project leverages a robust modern stack and Clean Architecture principles via the Repository Pattern.

-   **Backend:** Laravel 12 API (PHP 8.2)
-   **Frontend:** Vue 3 (Composition API) + Vue Router + Pinia + TypeScript
-   **Styling:** Custom CSS + Bootstrap (Metronic structure)
-   **Database:** PostgreSQL 14+
-   **Service:** Docker / docker-compose (also supports Laragon/Local server)

## Core Features

-   🔐 **Authentication & Security:** Secure login flow with Token-based auth (Sanctum), Role-Based Access Control (RBAC), password management, and 2FA capability.
-   💰 **Account Management:** Track balances across multiple physical or digital accounts.
-   🏷️ **Category Management:** Categorize income and expenses with customizable colors and icons.
-   💸 **Transaction Tracking:** Record incomes, expenses, and inter-account transfers. Updates account balances seamlessly.
-   📊 **Budgeting:** Set spending limits for specific categories and visually track progress with color-coded percentage bars.
-   🎯 **Financial Goals:** Set monetary goals with deadlines and actively track savings progress.
-   📅 **Bills & Subscriptions:** Manage recurring payments, get visual reminders for upcoming due dates, and mark bills as paid.
-   📈 **Reporting & Analytics:** View Monthly and Yearly cash flow summaries, category expense breakdowns via charts (ApexCharts), and export reports to Excel.
-   🤖 **AI-Powered Financial Insights:** Intelligent spending analysis and personalized recommendations powered by **Google Gemini 2.5 Flash**.
-   💬 **Telegram Integration:** 
    -   **Natural Language Entry:** Send messages like "Makan siang 50rb" or "Gaji 10jt" to automatically record transactions.
    -   **Interactive Bot:** Manage transactions via interactive buttons, select categories, and verify data before saving.
    -   **AI Analysis via Bot:** Request financial insights directly via the `/insight` command in Telegram.
-   🌓 **Dark Mode:** Full application support for light and dark themes with persistent preference.

## Tech Stack Details

### Backend (Laravel 12)
-   **Repository Pattern:** Decouples business logic from data access for better testability and maintenance.
-   **Job Queues:** Asynchronous processing for AI insights and Telegram notifications to ensure fast response times.
-   **API Documentation:** Integrated Swagger/OpenAPI support for endpoint testing.

### Frontend (Vue 3)
-   **Composition API:** Clean and reusable component logic.
-   **TypeScript:** Strict typing for better developer experience and reduced runtime errors.
-   **Vite:** Extremely fast HMR (Hot Module Replacement) and optimized production builds.

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
   ```
   *Note: Ensure you configure `GEMINI_API_KEY` and `TELEGRAM_BOT_TOKEN` in your .env file.*

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
   docker-compose exec node npm run dev (or run build for production)
   ```

The application will be accessible at `http://localhost:8000`.

### Installation (Local / Laragon)

1. **Setup Environment Variables:**
   ```bash
   cp .env.local .env
   ```
   *Edit `.env` to match your local PostgreSQL and Redis configuration.*

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

-   **Environment Switching:** Use the `switch-env.ps1` script to quickly flip `.env` and `vite.config.ts` between `local` and `docker`.
    ```powershell
    # Windows PowerShell
    .\switch-env.ps1 local
    .\switch-env.ps1 docker
    ```

-   **Telegram Webhook Setup:** To test Telegram locally, use `ngrok` or similar to expose your local port 8000 and set the webhook:
    ```bash
    curl https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://<NGROK_URL>/api/webhooks/telegram
    ```

## Licensing

This project is licensed under the MIT License.
