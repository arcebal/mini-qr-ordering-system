# QR Restaurant Ordering System

A Laravel-based restaurant ordering system for QR-driven, account-free customer ordering and authenticated restaurant administration.

## Features

### Customer ordering

- Public landing page for starting an order
- Customer name captured once in the session; no customer account is required
- Mobile-friendly menu grouped by category
- Product availability and stock visibility
- Session-based cart with quantity controls and subtotal calculation
- Checkout using the customer's name only
- Order confirmation page with an order number
- Customer order-status endpoint for tracking accepted, preparing, and completed orders

### Administration

- Authenticated dashboard
- Category management
- Product management, including image upload, replacement, price, stock, and availability
- Order list and order details
- Controlled order lifecycle: `accepted` → `preparing` → `completed`
- Order deletion that preserves the order items while marking the order as `deleted`
- Automatic stock deduction when an order is placed
- Automatic sold-out handling when stock reaches zero
- Dashboard metrics and charts based on completed orders
- Date-filtered reports with PDF and Excel export

## Technology stack

- PHP 8.3+
- Laravel 13
- Laravel Breeze authentication
- Blade templates
- Tailwind CSS and Vite
- Alpine.js
- Chart.js
- SQLite by default, with Laravel-supported database drivers available
- `barryvdh/laravel-dompdf` for PDF reports
- `maatwebsite/excel` for Excel reports

## Requirements

Install the following before setting up the project:

- PHP 8.3 or newer
- Composer
- Node.js and npm
- A database supported by Laravel (SQLite is the default configuration)

## Installation

Clone the repository and enter the project directory:

```bash
git clone <repository-url>
cd qr-restaurant-system
```

Install the PHP and JavaScript dependencies, create the environment file, generate the application key, run migrations, and build the frontend assets:

```bash
composer run setup
```

The setup script runs the equivalent of:

```bash
composer install
php artisan key:generate
php artisan migrate --force
npm install --ignore-scripts
npm run build
```

If using uploaded product images, create Laravel's public storage link:

```bash
php artisan storage:link
```

For local development, start the application and Vite watcher with:

```bash
composer run dev
```

The application is normally available at [http://localhost:8000](http://localhost:8000).

## Environment configuration

The example environment is configured for MySQL. Set the `DB_*` variables in `.env` or Railway to match your database, and configure `APP_URL` to the URL used by the restaurant's QR code.

For Railway's Railpack builder, add this service variable so the required PHP extensions are installed during the build:

```dotenv
RAILPACK_PHP_EXTENSIONS=gd,redis
```

Set `APP_ENV=production`, `APP_DEBUG=false`, and generate a unique production `APP_KEY`. Keep database and Cloudinary credentials in Railway variables rather than committing them to the repository.

For local development, mail is sent to the log by default and queued work uses the database queue. No external mail or payment provider is required for the current ordering flow.

## Usage

### Customer flow

```text
Open QR URL → Enter name → Browse menu → Add to cart → Checkout → Track order
```

The customer entry point is `/`. After a name is entered, the menu is available at `/menu`.

### Admin flow

```text
Register/login → Dashboard → Manage categories/products → Review orders → Update status → View reports
```

Authenticated administration is available under `/admin`. The dashboard is available at `/admin/dashboard`.

## Important routes

| Area | Route | Purpose |
| --- | --- | --- |
| Customer | `/` | Start an order and enter a customer name |
| Customer | `/menu` | Browse available products |
| Customer | `/cart` | Review and edit the cart |
| Customer | `/checkout` | Place an order |
| Admin | `/admin/dashboard` | View operational and sales metrics |
| Admin | `/admin/categories` | Manage categories |
| Admin | `/admin/products` | Manage products and stock |
| Admin | `/admin/orders` | Manage incoming orders |
| Admin | `/admin/reports` | View and export reports |

## Order and inventory behavior

New orders are created with status `accepted` and payment status `unpaid`. An administrator can advance an order only through the supported sequence:

```text
accepted → preparing → completed
```

When checkout succeeds, the application validates current stock inside a database transaction, creates the order and order items, deducts stock, and marks products unavailable when their stock reaches zero. If the cart exceeds current stock, the order is not created.

Deleted orders use the `deleted` status and remain available for historical reference. Deleted orders are excluded from active order lists and completed-sales reporting.

## Testing

Run the complete feature and unit test suite with:

```bash
composer run test
```

The suite covers authentication, customer ordering, cart validation, inventory deduction, admin catalog management, order transitions, and PDF/Excel reporting.

## Project structure

```text
app/
├── Http/Controllers/Customer   Customer menu, cart, checkout, and order status
├── Http/Controllers/Admin      Dashboard, catalog, orders, and reports
├── Models                      User, Category, Product, Order, and OrderItem
├── Services                    Report aggregation and dashboard metrics
└── Exports                     Excel report exports
database/migrations              Application schema and order-status changes
resources/views                  Customer, admin, auth, and shared Blade views
routes/web.php                   Customer and admin web routes
tests/Feature                    Authentication, ordering, admin, and reporting tests
```

## Development notes

- Keep the customer checkout limited to the customer's name unless the requirements change.
- Only available products with sufficient stock should be orderable.
- Completed orders are the source for revenue and sales metrics.
- Product and category deletion must preserve historical order integrity.
- Run the test suite after changes to order status, inventory, or reporting logic.

## License

This project is based on Laravel and is distributed under the MIT license unless a separate project license is added.
