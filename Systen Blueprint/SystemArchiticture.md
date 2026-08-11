# Booking System Architecture

## Application Stack

- Laravel 13
- Blade server-rendered views
- MySQL-backed Eloquent models and migrations
- PHPUnit feature tests

## Domain Model

- `Service`: a bookable offering with name, description, duration, price, and active status.
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
- Service management: `/admin/services` lets admins create, edit, and disable rooms.
- Calendar export: `GET /admin/calendar.ics` downloads confirmed bookings.

## Access Roles

- `admin`: can access reservation dashboard, service management, booking status updates, and calendar export.
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
