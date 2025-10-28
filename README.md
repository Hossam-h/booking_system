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
- Node 18+ (only if you later add front-end assets)

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
   - `php artisan migrate`
   - `php artisan db:seed` (optional)
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

## Validation (Form Requests)
- `RegisterRequest`, `LoginRequest`
- `EventStoreRequest`, `EventUpdateRequest`
- `TicketRequest`
- `CreateBookingRequest`
- `PaymentRequest`

## Authorization (Policies)
- User roles on `users.role`: `admin`, `organizer`, `customer`
- `EventPolicy`: create/update/delete restricted to admin or owning organizer
- `TicketPolicy`, `BookingPolicy`: enforce role/ownership actions
- Controllers call `$this->authorize(...)` for access control

## Response Shape
All responses use BaseController helpers:
- Success: `sendResponse(data, message, addtionalData?)`
- Error: `sendError(message, errors?, code?)`
- Not authorized: `sendNotAuthorized([], message)`

Global JSON handlers ensure consistent 401/403 responses for unauthenticated/unauthorized requests.

## Caching
- Event index uses cache (10 minutes) with a composite key of query params
- `Event` model flushes cache on create/update/delete

## Middleware
- Custom middleware alias registration in `bootstrap/app.php`:
```
$middleware->alias([
  'prevent.double.booking' => App\\Http\\Middleware\\PreventDoubleBooking::class,
]);
```
- Used on booking routes to prevent race conditions

## Testing
- PHPUnit 11 configured
- Feature tests:
  - `tests/Feature/AuthFeatureTest.php`
  - `tests/Feature/EventFeatureTest.php`
  - `tests/Feature/BookingFeatureTest.php`
  - `tests/Feature/PaymentFeatureTest.php`
- Unit tests:
  - `tests/Unit/PaymentServiceTest.php`

### Test DB Configuration
- Create `.env.testing` (MySQL example):
```
APP_ENV=testing
APP_KEY=base64:GENERATE_ONE
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking_test
DB_USERNAME=root
DB_PASSWORD=secret
CACHE_STORE=array
QUEUE_CONNECTION=sync
MAIL_MAILER=array
SESSION_DRIVER=array
```
- Run: `php artisan test`
- Filter: `php artisan test --filter=PaymentFeatureTest`

## Example Requests
- Create Event (Organizer)
```
POST /api/events
Authorization: Bearer <token>
{
  "title": "Tech Conference",
  "description": "Annual tech event",
  "date": "2025-12-01",
  "location": "NYC"
}
```

- Book Ticket
```
POST /api/tickets/{ticket}/bookings
Authorization: Bearer <token>
{
  "quantity": 2
}
```

- Pay for Booking
```
POST /api/bookings/{id}/payment
Authorization: Bearer <token>
```

## Project Scripts
- Composer scripts:
  - `composer test` -> clears config then runs `php artisan test`
  - `composer dev`  -> runs server, queue (listen), logs, and Vite concurrently

## Notes
- Notifications (e.g., `BookingConfirmedNotification`) implement `ShouldQueue`. Ensure a queue worker is running for async delivery in non-testing environments.
- Sanctum protects authenticated routes; include bearer token in requests.

---

MIT License
