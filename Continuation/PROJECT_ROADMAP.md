# QR Restaurant Ordering System
## Development Roadmap & Continuation Prompt

---

## Project Overview

This project is a **QR Restaurant Ordering System** built with:

- Laravel 12
- PHP 8.x
- MySQL Community Server
- Bootstrap 5
- Laravel Breeze Authentication
- SweetAlert2
- HeidiSQL for local development

The goal is a professional QR-based restaurant ordering system where administrators manage the menu, inventory, and orders, while customers scan one QR code, browse the menu without an account, add items to a cart, enter only their name at checkout, and submit an order to the restaurant.

## Development Rules

- Provide complete, copy-and-paste files whenever possible; avoid edits that require manually searching through files.
- Build and test one phase at a time before moving to the next.
- Keep Bootstrap 5 and SweetAlert2 as the UI foundation.
- Keep the customer experience simple, mobile-friendly, and professional.
- Use maintainable Laravel conventions and best practices.

---

## Completed Phases

### ✅ Phase 1 — Project Setup (Complete)

- Laravel 12 installation
- MySQL configuration
- Laravel Breeze authentication
- Storage link
- Bootstrap admin layout and navigation
- Admin login and project structure

### ✅ Phase 2 — Database Design (Complete)

Current tables:

- `users`
- `categories`
- `products`

Relationship:

```text
Category hasMany Products
Product belongsTo Category
```

`orders` and `order_items` will be introduced as part of the ordering work.

### ✅ Phase 3 — Models & Relationships (Complete)

- Category and Product models
- Fillable fields
- Relationships
- Database migrations

### ✅ Phase 4 — Authentication & Admin (Complete)

- Login and logout
- Protected admin routes
- Dashboard routing
- Admin layout and navigation

### ✅ Phase 5 — Dashboard (Complete)

- Live category and product counters
- Dashboard cards and responsive layout

Future dashboard statistics will include sales, revenue, pending orders, and low-stock products.

### ✅ Phase 6 — Category Management (Complete)

- List, create, edit, and delete categories
- Validation, success messages, and pagination

Soft delete, trash, and restore are intentionally postponed to Phase 12.

### ✅ Phase 7 — Product Management & Admin UI/UX (Complete)

- Create, edit, and delete products
- Product image upload, replacement, and deletion
- Category assignment, price, stock, and availability
- Improved product table and dashboard
- SweetAlert2 notifications and delete confirmations

---

## Current Status

Phases 1–11 are complete. The admin dashboard now provides completed-order reporting, sales charts, date-filtered insights, and PDF/Excel exports. Continue from **Phase 12 — Advanced Features**.

| Phase | Status |
| --- | --- |
| 1–7 — Admin foundation, management, and UI/UX | ✅ Complete |
| 8 — Customer QR Ordering | ✅ Complete |
| 9 — Admin Order Management | ✅ Complete |
| 10 — Inventory Automation | ✅ Complete |
| 11 — Reports | ✅ Complete |
| 12 — Advanced Features | 🚀 Next |

---

## 🚀 Phase 8 — Customer QR Ordering System

This is the current priority. It turns the project into a real QR ordering system.

### Phase 8.1 — Public Customer Menu

One QR code opens the restaurant menu.

```text
Scan QR → Restaurant Menu
```

Requirements:

- No login or registration
- Public route
- Mobile-friendly design
- Customers only provide their name at checkout

### Phase 8.2 — Browse Menu

Display for each product:

- Image
- Name
- Description
- Price
- Availability

Only available products should be orderable. Out-of-stock products must display **Out of Stock** and cannot be added to the cart.

### Phase 8.3 — Shopping Cart

Customers can:

- Add products
- Increase or decrease quantity
- Remove items
- See the subtotal
- Proceed to checkout

### Phase 8.4 — Checkout

Keep checkout intentionally simple:

```text
Customer Name
[ Place Order ]
```

Do not request a phone number or email address.

### Phase 8.5 — Order Success Page

After submission, show a clear confirmation, such as:

```text
Thank you!
Order #0001
Please wait while we prepare your order.
```

---

## 🚀 Phase 9 — Admin Order Management

The admin receives customer orders immediately and can manage them through this lifecycle:

```text
Pending → Preparing → Ready → Completed
```

Admin capabilities:

- View incoming orders and details
- Accept or reject orders
- Change order status
- Support cancelled/rejected orders where appropriate

Consider a kitchen display/order queue view for easy-to-read **Pending**, **Preparing**, and **Ready** orders during demonstrations.

---

## 🚀 Phase 10 — Inventory Automation

When an order is placed:

- Deduct the ordered quantity from product stock automatically.
- If stock reaches zero, mark the product unavailable.
- Prevent customers from ordering unavailable products.

Example:

```text
Burger stock: 20 → 19
```

---

## 🚀 Phase 11 — Reports

Add dashboard statistics and reporting for:

- Today's orders
- Today's revenue
- Monthly revenue
- Best-selling product
- Low-stock products
- Most-ordered category

Exports:

- PDF
- Excel

---

## 🚀 Phase 12 — Advanced Features

### Soft Delete and Trash

Implement the deferred data-management features consistently across categories and products:

- Soft delete
- Trash bin
- Restore
- Permanent delete

### Search and Filters

Search:

- Products
- Categories
- Orders

Filters:

- Category
- Availability
- Stock
- Order status
- Date

### QR Code Management

- Generate the restaurant QR code
- Download PNG and SVG
- Print QR code

### Dashboard and UI Improvements

- Recent orders
- Low-stock alerts
- Revenue charts
- Top-selling products
- Restaurant/POS-style theme
- Modern sidebar, cards, and responsive layout

---

## Final User Flow

```text
Admin
Login → Dashboard → Manage Categories → Manage Products → Generate QR → Print QR

Customer
Scan QR → Browse Menu → Add to Cart → Checkout → Enter Name → Submit Order

Admin
Receive Order → Preparing → Ready → Completed
```

---

## Continuation Prompt

Continue development of this Laravel 12 QR Restaurant Ordering System from **Phase 8 — Customer QR Ordering System**.

Phases 1–7 are complete, including the admin panel, category and product management, and admin UI/UX improvements. Treat them as complete unless a specific bug needs fixing.

Build the public, mobile-friendly customer menu opened by one QR code. Customers must not log in or register. They should browse available products, add them to a cart, adjust quantities, see the subtotal, enter only their name at checkout, place the order, and see an order-success page with an order number. Then continue through Phases 9–12 in the order defined above.

Use Laravel best practices, Bootstrap 5, and SweetAlert2. Provide complete copy-and-paste code files whenever possible, validate and test each phase before proceeding, and keep the customer flow simple and professional.
