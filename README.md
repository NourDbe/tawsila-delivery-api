Tawsila Delivery API

A RESTful API built with Laravel for managing delivery orders, customers, stores, drivers, and order items.

The project focuses on clean architecture, reusable query logic, database optimization, and consistent JSON responses.

Features

- RESTful API endpoints for order and store management.
- Order creation with associated order items.
- Order status management using a PHP Enum.
- Eloquent relationships between customers, stores, drivers, orders, and order items.
- Reusable Local Scopes for common order queries.
- A Global Scope to exclude suspended stores from regular store listings.
- An explicit Global Scope bypass for administrative store listings.
- Service classes to separate business logic from controllers.
- Service Container binding for delivery fee calculation.
- API Resources for consistent JSON serialization.
- Form Request validation.
- Database indexes for frequently queried order fields.
- Eager loading and relationship counts to help prevent N+1 queries.
- Automated unit and feature tests.

Tech Stack

- PHP
- Laravel
- MySQL or another Laravel-supported database
- PHPUnit
- Eloquent ORM

Requirements

Make sure the following are installed:

- PHP version compatible with the Laravel version used by this project.
- Composer
- A supported database server for local development.

Installation

Clone the repository:

git clone https://github.com/NourDbe/tawsila-delivery-api.git
cd tawsila-delivery-api

Install PHP dependencies:

composer install

Create the environment configuration file:

cp .env.example .env

On Windows Command Prompt, you can use:

copy .env.example .env

Generate the application key:

php artisan key:generate

Configure the database connection in the ".env" file before running migrations.

For example, with MySQL:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tawsila_delivery
DB_USERNAME=root
DB_PASSWORD=

Create the database if it does not already exist, then run:

php artisan migrate --seed

«The database configuration above is an example. Use the settings appropriate for your local environment.»

Running the Application

Start the Laravel development server:

php artisan serve

The default local address is:

http://127.0.0.1:8000

API routes are defined in "routes/api.php" and use the "/api" prefix.

API Endpoints

The following endpoints are implemented in the project.

Method| Endpoint| Description
GET| "/api/orders/current"| Retrieve current orders
GET| "/api/orders/today"| Retrieve today's orders
GET| "/api/orders/unassigned-delayed"| Retrieve delayed orders that have no assigned driver
GET| "/api/drivers/{driver}/orders/current"| Retrieve current orders for a specific driver
POST| "/api/orders"| Create an order with its items
GET| "/api/stores"| Retrieve stores visible to regular users
GET| "/api/admin/stores"| Retrieve stores for administrative use, including suspended stores

Listing Current Orders

GET /api/orders/current

Optional query parameters:

Parameter| Type| Description
"per_page"| Integer| Number of records per page; valid range: 1–100

Example:

GET /api/orders/current?per_page=20

Listing Today's Orders

GET /api/orders/today

This endpoint retrieves orders created today using the application's date-based query logic.

Listing Delayed Unassigned Orders

GET /api/orders/unassigned-delayed?minutes=30

The "minutes" parameter specifies the delay threshold and must be between 1 and 1440.

Listing Current Orders for a Driver

GET /api/drivers/1/orders/current

Replace "1" with the relevant driver ID.

Creating an Order

POST /api/orders
Content-Type: application/json
Accept: application/json

Example request body:

{
  "customer_id": 1,
  "store_id": 1,
  "delivery_address": "Damascus, Syria",
  "items": [
    {
      "item_name": "Package A",
      "unit_price": 15000,
      "quantity": 2
    },
    {
      "item_name": "Package B",
      "unit_price": 10000,
      "quantity": 1
    }
  ]
}

The order is created together with its associated items in a database transaction. The delivery fee is calculated through the configured delivery fee service.

The example IDs must refer to existing records, and the request must satisfy the validation rules defined by the application.

Listing Stores

Regular store listing:

GET /api/stores

Administrative listing:

GET /api/admin/stores

Regular store listings exclude suspended stores through a Global Scope. The administrative query intentionally bypasses that scope so suspended stores can also be retrieved.

Database Design

The main entities are:

- Customer: Stores customer information.
- Store: Represents a store and includes its suspension status.
- Driver: Represents a delivery driver.
- Order: Stores order details and references its customer, store, and optionally assigned driver.
- OrderItem: Stores the items associated with an order, including item name, unit price, and quantity.

Relationships

- A customer can have many orders.
- A store can have many orders.
- A driver can have many orders.
- An order belongs to a customer and a store.
- An order may belong to a driver.
- An order has many order items.
- Each order item belongs to one order.

Order items retain their item names and unit prices as order-specific snapshots.

Order Statuses

Order statuses are represented by the "OrderStatus" Enum.

Status| Value
Pending| "pending"
Accepted| "accepted"
Picked Up| "picked_up"
Delivered| "delivered"
Cancelled| "cancelled"

The active statuses are "pending", "accepted", and "picked_up".

The terminal statuses are "delivered" and "cancelled".

Query Optimization

Database Indexes

The orders table includes indexes designed to support common filtering and sorting operations:

- "(status, created_at)" supports queries filtering by order status and sorting or filtering by creation time.
- "(driver_id, status, created_at)" supports driver-specific and unassigned-order queries.
- "created_at" supports date-based queries, including today's orders.

These indexes are intended to improve query efficiency for the corresponding access patterns. Actual performance depends on the database engine, data distribution, and query execution plan.

Local Scopes

Reusable Eloquent Local Scopes encapsulate common order query conditions, including:

- Active orders.
- Orders belonging to a particular driver.
- Unassigned orders.
- Orders older than a specified number of minutes.
- Orders created today.
- Orders sorted by creation time.

This reduces duplicated query logic and improves maintainability.

Global Scope

The "ExcludeSuspendedStoresScope" excludes suspended stores from regular store queries.

Administrative queries explicitly bypass this scope when suspended stores must be included.

Order queries that need to display historical store information also intentionally bypass the scope when eager-loading the related store.

Preventing N+1 Queries

The order query service uses eager loading for related customer, store, and driver data, where needed.

It also uses "withCount('items')" to retrieve item counts without loading every item just to count it.

Lazy-loading prevention is enabled outside production to help identify accidental lazy-loading queries during development.

Service Layer and Dependency Injection

Business logic is separated from controllers through service classes.

The delivery fee calculator is defined through an interface and bound to a standard implementation in the Laravel service container.

This allows the fee calculation implementation to be replaced or tested independently.

Controllers are responsible for handling requests and responses, while services handle application operations.

Validation and Error Responses

Request validation is handled through Laravel Form Requests.

The API uses JSON responses for successful requests and errors. The exception handling configuration maps relevant failures to appropriate HTTP status codes, including validation errors and missing resources.

Clients should send:

Accept: application/json

Testing

Run the automated test suite:

php artisan test

Run the order query-count test separately:

php artisan test --filter=OrderQueryCountTest

The query-count feature test measures database queries for "GET /api/orders/current" with different numbers of orders.

Query Count Measurement

Measured in the local automated test environment:

Number of Orders| Database Queries
5| 3
15| 3

The query count remained constant when the number of orders increased from 5 to 15 in this test scenario.

This result supports the goal of preventing query growth proportional to the number of returned orders for this endpoint. It is a test-specific measurement, not a guarantee of identical query counts in every environment.

Project Structure

app/
├── Enums/
├── Exceptions/
├── Http/
│   ├── Controllers/Api/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Providers/
├── Scopes/
├── Services/
└── Traits/

database/
├── factories/
├── migrations/
└── seeders/

routes/
└── api.php

tests/
├── Feature/
└── Unit/

License

Add the applicable license information before distributing this project.
