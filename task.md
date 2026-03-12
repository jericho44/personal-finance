# Personal Finance Management App - Action Plan

## 1. Authentication Review & UI Implementation
- [] Verify template's existing auth meets requirements. (Completed - API endpoints are present)
- [ ] Create Vue View and Route for `Register` (`/register`)
- [ ] Create Vue View and Route for `Forgot Password` / `Reset Password`
- [ ] Create Vue View for `User Profile` (Edit details, change password)
- [ ] Create Vue View for `2FA Setup` (Displaying QR Code and handling setup via the existing auth endpoints)
- [ ] Update frontend store/auth logic to handle registering and 2FA process.

## 2. Categories Management
- [ ] Create `Category` Model and Migration (with type: income/expense, color, icon)
- [ ] Create Repository and Interface for Categories
- [ ] Create API Controller (`CategoryController`) with CRUD
- [ ] Setup Vue Router, Store (Pinia), and API Service for Categories
- [ ] Build Category UI (List, Create/Edit Form, Delete)

## 3. Account Management
- [ ] Create `Account` Model and Migration (name, type, balance, currency, etc.)
- [ ] Create Repository and Interface for Accounts
- [ ] Create API Controller (`AccountController`) with CRUD
- [ ] Setup Vue Router, Store (Pinia), and API Service for Accounts
- [ ] Build Account UI (List, Create/Edit Form, Delete)

## 4. Transaction Management
- [ ] Create `Transaction` Model and Migration (account_id, category_id, type, amount, date, notes)
- [ ] Create Repository and Interface for Transactions
- [ ] Create API Controller (`TransactionController`) with CRUD and Filtering
- [ ] Setup Vue Router, Store, and API Service for Transactions
- [ ] Build Transaction UI (List, Filtering, Add Income/Expense Form)

## 5. Budget Management
- [ ] Create `Budget` Model and Migration (category_id, amount, period)
- [ ] Create Repository and Interface for Budgets
- [ ] Create API Controller (`BudgetController`)
- [ ] Setup Vue Router, Store, and API Service for Budgets
- [ ] Build Budget UI (Setup, Progress Tracking)

## 6. Dashboard & Analytics
- [ ] Create API endpoints for Dashboard statistics (Net Worth, Income vs Expense, Spending by Category)
- [ ] Build Dashboard UI with Charts (using apexcharts)

## 7. Infrastructure Setup (Docker Compliance)
- [ ] Create `docker-compose.yml` (app, nginx, node, mysql, redis, queue-worker)
- [ ] Create `Dockerfile` (Laravel + multi-stage ready)
- [ ] Create `/docker/nginx/default.conf`
- [ ] Create `/docker/php/php.ini`
- [ ] Configure `.env` to rely on Docker services (mysql, redis)
