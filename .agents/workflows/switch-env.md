---
description: Switch between local (Laragon) and Docker environments
---

# Switch Environment

This project supports two runtime environments: **Local (Laragon)** and **Docker**.

## Quick Switch

Use the PowerShell script at the project root. If you get a "scripts are disabled" error, run it with the bypass flag:

```powershell
# Switch to local (Laragon)
powershell -ExecutionPolicy Bypass -File .\switch-env.ps1 local

# Switch to Docker
powershell -ExecutionPolicy Bypass -File .\switch-env.ps1 docker
```

## Manual Switch

Copy the appropriate `.env` file:

```powershell
# For local
Copy-Item .env.local .env

# For Docker
Copy-Item .env.docker .env
```

Then clear the config cache:

```powershell
# Local
php artisan config:clear

# Docker
docker compose exec app php artisan config:clear
```

## Key Differences

| Setting        | Local (Laragon)     | Docker                  |
|----------------|---------------------|-------------------------|
| DB_HOST        | 127.0.0.1           | postgresql (container)  |
| DB_PASSWORD    | (empty)             | secret                  |
| REDIS_HOST     | 127.0.0.1           | redis (container)       |
| CACHE_DRIVER   | file                | redis                   |
| QUEUE_CONNECTION | sync              | redis                   |
| SESSION_DRIVER | file                | redis                   |
| MAIL_HOST      | 127.0.0.1           | mailhog (container)     |
| APP_URL        | http://127.0.0.1:8000 | http://localhost:8000 |

## Running Locally (Laragon)

// turbo
1. Switch to local env: `.\switch-env.ps1 local`

2. Make sure PostgreSQL is running in Laragon

3. Run migrations and seeders:
```powershell
php artisan migrate --seed
```

4. Start Laravel dev server:
```powershell
php artisan serve
```

5. Start Vite dev server (in a separate terminal):
```powershell
npm run dev
```

6. Access the app at: http://127.0.0.1:8000

## Running with Docker

// turbo
1. Switch to Docker env: `.\switch-env.ps1 docker`

2. Start all containers:
```powershell
docker compose up -d --build
```

3. Run migrations and seeders:
```powershell
docker compose exec app php artisan migrate --seed
```

4. Access the app at: http://localhost:8000