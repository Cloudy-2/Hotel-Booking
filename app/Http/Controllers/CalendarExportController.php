<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CalendarExportController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $bookings = Booking::with('service')
            ->where('status', Booking::STATUS_CONFIRMED)
            ->orderBy('starts_at')
            ->get();

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Aurelia Hotel//Booking Calendar//EN',
            'CALSCALE:GREGORIAN',
        ];

        foreach ($bookings as $booking) {
            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:booking-'.$booking->id.'@aurelia.local';
            $lines[] = 'DTSTAMP:'.now()->utc()->format('Ymd\THis\Z');
            $lines[] = 'DTSTART:'.$booking->starts_at->utc()->format('Ymd\THis\Z');
            $lines[] = 'DTEND:'.$booking->ends_at->utc()->format('Ymd\THis\Z');
            $lines[] = 'SUMMARY:'.static::escape($booking->service->name.' - '.$booking->customer_name);
            $lines[] = 'DESCRIPTION:'.static::escape($booking->customer_email.' '.$booking->customer_phone);
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        return response(implode("\r\n", $lines), 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="aurelia-bookings.ics"',
        ]);
    }

    private static function escape(?string $value): string
    {
        return str_replace(["\\", "\n", "\r", ',', ';'], ['\\\\', '\\n', '', '\\,', '\\;'], $value ?? '');
    }
}
