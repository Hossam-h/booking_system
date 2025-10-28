# Booking System API (Laravel 12)

A role-based booking platform API where organizers create events and tickets, customers book tickets and pay, and admins oversee the system. Built with Laravel 12 and Sanctum, featuring authorization policies, request validation, caching, and a consistent API response schema.

## Features
- Authentication (register, login, logout, current user) via Sanctum
- Events: list (search/filter/paginate), show (with tickets), create/update/delete (organizer/admin)
- Tickets: create/update/delete (organizer/admin), list per event
- Bookings: create, list own, cancel
- Payments: process booking payment, confirm booking, notify user
- Policies for `admin`, `organizer`, `customer` roles
- Form Requests for validation
- Cache layer for event listing
- Consistent JSON responses via BaseController

## Tech Stack
- PHP 8.2+, Laravel 12, Sanctum
- MySQL (recommended) or SQLite (for simple local/dev)
- PHPUnit for tests

## Requirements
- PHP 8.2+
- Composer
- MySQL 8+ (or MariaDB) or SQLite

## Setup
1. Clone the repo
2. Install dependencies
   - `composer install`
3. Create environment file
   - `cp .env.example .env`
4. Generate app key
   - `php artisan key:generate`
5. Configure database in `.env`
   - Example (MySQL):
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking
DB_USERNAME=root
DB_PASSWORD=secret
```
6. Run migrations and seeders (optional seeders available)
   - `php artisan migrate:fresh --seed`
7. Start the server
   - `php artisan serve`

## Authentication
- Issue tokens via `POST /api/login` and `POST /api/register` (Sanctum)
- Include token in header for protected routes:
  - `Authorization: Bearer <token>`

## API Overview
- Auth
  - `POST /api/register` Register (role defaults to customer)
  - `POST /api/login` Login and get token
  - `POST /api/logout` Logout (requires auth)
  - `GET  /api/me` Current user (requires auth)

- Events (requires auth)
  - `GET    /api/events` List with `q`, `location`, `date` or `from`/`to`, `per_page`
  - `GET    /api/events/{id}` Show with tickets
  - `POST   /api/events` Create (organizer/admin)
  - `PUT    /api/events/{id}` Update (owner organizer/admin)
  - `DELETE /api/events/{id}` Delete (owner organizer/admin)

- Tickets (requires auth)
  - `POST   /api/events/{event_id}/tickets` Create (organizer/admin)
  - `PUT    /api/tickets/{ticket}` Update (owner organizer/admin)
  - `DELETE /api/tickets/{ticket}` Delete (owner organizer/admin)

- Bookings (requires auth)
  - `POST /api/tickets/{ticket}/bookings` Create booking
  - `GET  /api/bookings` List current user bookings
  - `PUT  /api/bookings/{id}/cancel` Cancel (owner/admin)

- Payments (requires auth)
  - `POST /api/bookings/{id}/payment` Process payment for a booking
  - `GET  /api/payments/{id}` Show payment (owner/admin)

## Middleware
- Custom middleware alias registration in `bootstrap/app.php`:
```
$middleware->alias([
  'prevent.double.booking' => App\\Http\\Middleware\\PreventDoubleBooking::class,
]);
```

## Testing
- Run: `php artisan test`