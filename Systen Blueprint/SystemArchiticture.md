# Booking System Architecture

## Application Stack

- Laravel 13
- Blade server-rendered views
- MySQL-backed Eloquent models and migrations
- PHPUnit feature tests

## Domain Model

- `Service`: a bookable room offering with name, description, uploaded/managed images, gallery, amenities, guest capacity, room size, duration, price, and active status.
- `Booking`: a customer reservation for one service with start/end times, contact details, notes, and status.
- `User`: an authenticated account with `admin` or `customer` role.
- `AvailabilityRule`: opening and closing windows by weekday.
- `Holiday`: date-specific closures.

Local database settings live in `.env` and currently point to database `booking` on `127.0.0.1:3306`.

## Main Flows

- Guest site: `GET /` shows active room experiences.
- Dashboard: `GET /admin/reservations` shows booking counts and recent bookings.
- Create booking: `GET /bookings/create` renders the request form.
- Store booking: `POST /bookings` validates input, calculates end time, rejects overlaps, and stores the request as pending.
- Update status: `PATCH /bookings/{booking}/status` marks a booking confirmed or cancelled.
- Service management: `/admin/services` lets admins create, edit, disable, and visually manage rooms.
- Availability management: `/admin/availability` lets admins edit weekly reservation hours and holiday closures.
- Calendar view: `GET /admin/calendar` shows confirmed bookings by arrival date.
- Calendar export: `GET /admin/calendar.ics` downloads confirmed bookings.

## Access Roles

- `admin`: can access reservation dashboard, service management, availability settings, booking status updates, calendar view, and calendar export.
- `customer`: can sign in but cannot access admin routes.
- Guests can browse rooms and submit reservation requests.

Seeded admin login:

```text
admin@aurelia.test
password
```

## Conflict Rule

A booking conflicts when another pending or confirmed booking exists for the same service where:

```text
existing.starts_at < new.ends_at
existing.ends_at > new.starts_at
```

Cancelled bookings are ignored by the conflict check.

## Test Coverage

The feature test suite runs against sqlite `:memory:` via `phpunit.xml`, keeping automated tests isolated from the local MySQL `booking` database.

Current verification status:

```text
17 tests passed
80 assertions passed
```
