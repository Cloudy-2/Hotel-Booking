# Booking System Architecture

## Application Stack

- Laravel 13
- Blade server-rendered views
- MySQL-backed Eloquent models and migrations
- SQLite by default for local development
- PHPUnit feature tests

## Domain Model

- `Service`: a bookable offering with name, description, duration, price, and active status.
- `Booking`: a customer reservation for one service with start/end times, contact details, notes, and status.

Local database settings live in `.env` and currently point to database `booking` on `127.0.0.1:3306`.

## Main Flows

- Dashboard: `GET /` shows active services, booking counts, and recent bookings.
- Create booking: `GET /bookings/create` renders the request form.
- Store booking: `POST /bookings` validates input, calculates end time, rejects overlaps, and stores the request as pending.
- Update status: `PATCH /bookings/{booking}/status` marks a booking confirmed or cancelled.

## Conflict Rule

A booking conflicts when another pending or confirmed booking exists for the same service where:

```text
existing.starts_at < new.ends_at
existing.ends_at > new.starts_at
```

Cancelled bookings are ignored by the conflict check.
