## Store Billing — Retail Counter Order System

A small Laravel application that lets a retail counter record customer orders against a product catalog while keeping stock in sync. It exposes both a web UI (matching the provided wireframe) and a JSON API for creating orders and querying customer history.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [Running the Queue Worker](#running-the-queue-worker)
- [API Reference](#api-reference)
- [Testing](#testing)
- [Project Structure](#project-structure)
- [Assumptions](#assumptions)
- [Design Decisions](#design-decisions)
- [Troubleshooting](#troubleshooting)
  
---

## Features

- **Product Catalog** — name, unique code, price, tax percentage, stock on hand.
- **Customers** — identified by unique email; auto-created on first order.
- **Order Creation** — one customer, one or more product lines, computed subtotal / tax / grand total, cash tendered, change returned.
- **Stock Sync** — atomic check-and-deduct with row-level locking; **no overselling** under concurrency.
- **Low Stock Alerts** — configurable threshold; surfaced on the dashboard and via API.
- **Order History** — paginated lookup by customer email with date-range filtering.
- **Queued Confirmation Email** — dispatched after commit; uses the log mail driver by default (no SMTP required).
- **Web UI** — order creation screen matching the provided wireframe.
- **JSON API** — for order creation, history, and low-stock queries.

---

## Tech Stack

| Layer | Choice |
|---|---|
| Framework | Laravel 11 |
| PHP | 8.2+ |
| Database | SQLite (dev/test), MySQL 8+ or PostgreSQL 14+ (production) |
| Queue | Database driver (default) or Redis |
| Mail | Log driver (dev), SMTP/Mailgun/SES (production) |
| Testing | PHPUnit + Laravel's HTTP test helpers |
| Frontend | Blade + vanilla CSS/JS (no build step required) |

---

## Requirements

- PHP **8.2** or newer
- Composer **2.x**
- SQLite (bundled with PHP) for local development
- MySQL 8+ or PostgreSQL 14+ for production (recommended for row-level locking)
- Node.js 18+ *(optional — only needed if you want to compile frontend assets)*

---

## Installation 

# 1. Clone the repository
git clone https://github.com/your-org/store-billing.git
cd store-billing

# 2. Install PHP dependencies
composer install

# 3. Copy the environment file
cp .env.example .env

# 4. Generate the application key
php artisan key:generate

# 5. Configuration
Create .env and copy .env.example - cp .env.example .env
# Database
- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=store_billing
- DB_USERNAME=root
- DB_PASSWORD=
  
# Queue
- QUEUE_CONNECTION=database

# Mail
- MAIL_MAILER=log
- MAIL_FROM_ADDRESS="billing@store.com"
- MAIL_FROM_NAME="${APP_NAME}"

# Application-specific settings
- LOW_STOCK_THRESHOLD=10

# 6. Database Creation and Run the Migration
- Create the database store_billing in XAMPP
php artisan migrate --seed

---

## Running the Application
php artisan serve

## URL of Project
- Visit the url http://127.0.0.1:8000 to get into the application.

---

## Running the Queue
php artisan queue:work

---

## API Reference
- All endpoints accept and return application/json. Base URL: http://localhost:8000/api.

# POST/orders
- Create a new order.
  Request body:

  {
    "customer_email": "thomas@example.com",
    "customer_name": "Thomas",
    "amount_given": 500,
    "items": [
        { "product_id": 1, "quantity": 2 },
       { "product_id": 2, "quantity": 5 }
      ]
  }
- Success (201 Created):
  {
    "data": {
        "id": 1,
        "customer": {
            "id": 11,
            "name": "Thomas",
            "email": "thomas@example.com"
        },
        "items": [
            {
                "id": 1,
                "product_id": 1,
                "product_name": "Colgate Strong Teeth Toothpaste 100g",
                "quantity": 2,
                "unit_price": 55,
                "line_total": 110
            }
        ],
        "subtotal": 285,
        "tax_total": 51.30,
        "grand_total": 336.30,
        "amount_given": 500,
        "change_returned": 163.70,
        "created_at": "2026-09-29T10:15:33+00:00"
    }
  }
- Errors:
  Status	Reason
  422	    Validation failure (email, items, product_id, quantity, amount)
  422	    Insufficient stock — {"success": false, "message": "Insufficient stock for 'Bread'. Available: 2, Requested: 5."}
  422	    Amount given < grand total

# GET /orders/history
- Paginated order history for a customer, resolved by email.
  GET /api/orders/history?email=thomas@example.com&per_page=10

- Success (201 Created):
{
    "success": true,
    "data": [ /* order summaries */ ],
    "meta": {
        "current_page": 1,
        "per_page": 10,
        "total": 3,
        "last_page": 1
    }
}
- Errors:
 Status	Reason
 404	No customer found with that email
 422	Malformed email

# GET /orders/history
- Returns products below the configured threshold (default 10).
  GET /api/products/low-stock?threshold=5&sort=name_asc

- Success (200):
  {
    "success": true,
    "meta": { "threshold": 5, "total": 2, "current_page": 1, "per_page": 15, "last_page": 1 },
    "data": [
        {
            "id": 3,
            "name": "Bread (large loaf)",
            "code": "BRD03",
            "price": 40,
            "tax_percentage": 0,
            "stock": 4,
            "threshold": 5,
            "deficit": 1
        }
    ]
} 

---

## Testing
php artisan test

# Test suites
Suite	                            What it covers
tests/Unit/OrderServiceTest.php	    Totals computation, tax aggregation, customer reuse, stock check, rollback, edge cases
tests/Unit/SendOrderConfirmationEmailTest.php	Job dispatches email; job skips when order is deleted
tests/Feature/Api/OrderApiTest.php	 API order creation, validation, 201/422 responses
tests/Feature/Api/OrderHistoryApiTest.php	History lookup, 404, pagination, date filters
tests/Feature/ConcurrentOrderTest.php	Concurrency invariant — no overselling when stock is limited

