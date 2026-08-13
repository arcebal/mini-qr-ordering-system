# Kusina Ni Aira QR Restaurant Ordering System

Kusina Ni Aira is a Laravel-based restaurant ordering system designed around a simple QR ordering experience. Customers scan the restaurant QR code, enter their name, browse the menu, place an order without creating an account, and track the order status. Restaurant administrators use a protected dashboard to manage the catalog, inventory, orders, payments, and sales reports.

## What the system provides

### Customer ordering

- Public, mobile-friendly ordering with no customer login or registration
- Customer name captured in the session for order identification
- Menu grouped by category
- Product images, descriptions, prices, availability, and stock limits
- Session-based cart with quantity controls and subtotal calculation
- Checkout with counter payment or mock online payment
- Order confirmation with a generated order number
- Automatic order-status tracking for `accepted`, `preparing`, and `completed` orders

### Restaurant administration

- Authenticated admin dashboard
- Category creation, editing, and deletion
- Product management with category, price, stock, availability, and image upload
- Incoming order list and detailed order view
- Payment tracking for unpaid and paid orders
- Controlled order lifecycle: `accepted` → `preparing` → `completed`
- Order deletion by marking the order as `deleted` while retaining its history
- Dashboard metrics and charts based on completed orders
- Date-filtered reports with PDF and Excel export

## Restaurant QR code

Customers can use the QR code below to open the restaurant ordering page.

![Kusina Ni Aira restaurant ordering QR code](Continuation/kusinaniairaQR.png)

### What happens after scanning

1. The QR code opens the application URL configured in `APP_URL`.
2. The customer enters their name. No account is required.
3. The customer browses the available menu and adds items to the cart.
4. At checkout, the customer selects `Pay at counter` or `Mock online payment`.
5. The order is submitted and the customer receives an order number.
6. The restaurant sees the order in the admin order queue.
7. The customer can keep the confirmation page open while the order moves through its status updates.

For a deployed restaurant installation, set `APP_URL` to the public URL encoded in the QR code. If the public URL changes, generate or replace the QR code so it points to the new address.

## User flows

### Customer flow

```text
Scan QR
  ↓
Enter name
  ↓
Browse menu
  ↓
Add items to cart
  ↓
Adjust quantities and review subtotal
  ↓
Choose payment method
  ↓
Place order
  ↓
Receive order number
  ↓
Track accepted → preparing → completed
```

Only products marked available and having stock greater than zero can be ordered. The system rechecks stock inside a database transaction when checkout is submitted. If another order has used the remaining stock, the current order is rejected instead of being created.

For counter payment, a new order starts as unpaid and must be marked as paid by an administrator before it can be completed. Mock online payment simulates a successful payment for testing and demonstration purposes; it is not a real payment gateway.

### Admin flow

```text
Log in
  ↓
Open dashboard
  ↓
Manage categories and products
  ↓
Maintain prices, images, availability, and stock
  ↓
Review incoming orders
  ↓
Mark counter payments as paid when received
  ↓
Move orders through accepted → preparing → completed
  ↓
Review dashboard metrics and export reports
```

Administrators can access the protected area under `/admin`. Non-admin authenticated users cannot access admin pages.

## Technology stack

- PHP 8.4 or newer
- Laravel 13
- Laravel Breeze authentication
- Blade templates
- Tailwind CSS, Vite, and Alpine.js
- Chart.js dashboard charts
- MySQL by default, with other Laravel-supported database drivers available
- Cloudinary for uploaded product images
- `barryvdh/laravel-dompdf` for PDF reports
- `maatwebsite/excel` for Excel reports

## Requirements

Install the following before setting up the project:

- PHP 8.4+
- Composer
- Node.js and npm
- MySQL or another database supported by Laravel
- PHP GD extension

## Installation

Clone the repository and enter the project directory:

```bash
git clone <repository-url>
cd qr-restaurant-system
```

Create the environment file, install dependencies, generate an application key, run migrations, and build the frontend assets:

```bash
composer run setup
```

The setup script performs the equivalent of:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
npm install --ignore-scripts
npm run build
```

On Windows, the setup script copies `.env.example` automatically through Composer, so the `cp` command above is only a description of the equivalent Unix operation.

Create the public storage link if product images use Laravel's local public disk or if the deployment requires the link:

```bash
php artisan storage:link
```

Update `.env` with the database and application settings before using the application:

```dotenv
APP_NAME="Kusina Ni Aira"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=qr_restaurant_system
DB_USERNAME=root
DB_PASSWORD=password
```

The database must exist before running migrations. For production, use a unique `APP_KEY`, set `APP_ENV=production`, set `APP_DEBUG=false`, and store database and Cloudinary credentials in deployment secrets rather than committing them.

### Cloudinary configuration

The default environment configuration uses Cloudinary for product image storage. Set the provider URL in `.env` or in the deployment platform:

```dotenv
FILESYSTEM_DISK=cloudinary
CLOUDINARY_URL=<cloudinary-connection-url>
```

For Railway's Railpack builder, add the required PHP extensions as a service variable:

```dotenv
RAILPACK_PHP_EXTENSIONS=gd,redis
```

### Start local development

Run the Laravel server, queue listener, log viewer, and Vite development server together:

```bash
composer run dev
```

The application is normally available at [http://localhost:8000](http://localhost:8000). If the QR code is being tested from a phone, the phone must be able to reach the computer hosting the application, and `APP_URL` should use an accessible network address rather than `localhost`.

## Admin access

The database seeder creates a development administrator when the database is seeded:

```text
Email:    admin@example.com
Password: password
```

Use these credentials only for local development or demonstrations. Change the password and use a controlled administrator account before deploying the system publicly.

To run the seeder explicitly:

```bash
php artisan db:seed
```

The admin login is available at `/login`, and the dashboard is available at `/admin/dashboard`.

## Important routes

| Area | Route | Purpose |
| --- | --- | --- |
| Customer | `/` | Start an order and enter a customer name |
| Customer | `/menu` | Browse available products |
| Customer | `/cart` | Review and edit the cart |
| Customer | `/checkout` | Select payment and place an order |
| Customer | `/orders/{order}` | View the order confirmation and status |
| Admin | `/login` | Admin authentication |
| Admin | `/admin/dashboard` | View operational and sales metrics |
| Admin | `/admin/categories` | Manage categories |
| Admin | `/admin/products` | Manage products, images, and stock |
| Admin | `/admin/orders` | Review and update orders |
| Admin | `/admin/reports` | View and export reports |

## Order, payment, and inventory behavior

New orders are created with status `accepted`. The supported status sequence is:

```text
accepted → preparing → completed
```

Orders using `Pay at counter` start as `unpaid`. An administrator can mark them as paid, after which they can be completed. `Mock online payment` starts as `paid` and exists only to simulate a successful online payment during testing or demonstrations.

When checkout succeeds, the application:

1. Locks the selected products for the transaction.
2. Verifies that every product is available and has enough stock.
3. Creates the order and its order items using the current product prices.
4. Deducts the ordered quantities from stock.
5. Marks products unavailable when their remaining stock reaches zero.
6. Clears the customer's cart and records the order in the customer's session.

Deleted orders are excluded from active order lists and completed-sales reporting. Their order data remains available for historical reference.

## Testing

Run the complete feature and unit test suite with:

```bash
composer run test
```

The tests cover authentication, admin authorization, profile management, customer menu and cart behavior, checkout validation, stock deduction, order status transitions, payment handling, catalog management, and PDF/Excel reporting.

## Project structure

```text
app/
├── Http/Controllers/Customer   Customer start, menu, cart, checkout, and status
├── Http/Controllers/Admin       Dashboard, catalog, orders, and reports
├── Http/Middleware              Customer-session and admin access checks
├── Models                       User, Category, Product, Order, and OrderItem
├── Services                     Report aggregation and dashboard metrics
└── Exports                      Excel report exports
database/migrations              Application schema and order-status changes
resources/views                  Customer, admin, auth, and shared Blade views
routes/web.php                   Customer and admin web routes
tests/Feature                    Authentication, ordering, admin, and reporting tests
Continuation/                    Project notes and Kusina Ni Aira QR assets
```

## Development notes

- Keep customer checkout limited to the current name and payment-method flow unless requirements change.
- Only available products with sufficient stock should be orderable.
- Completed orders are the source for revenue and sales metrics.
- Preserve product and category history needed by existing order records.
- Treat mock online payment as a test-only simulation, not a production payment integration.
- Run the test suite after changing order status, inventory, payment, or reporting logic.

## License

This project is based on Laravel and is distributed under the MIT license unless a separate project license is added.
