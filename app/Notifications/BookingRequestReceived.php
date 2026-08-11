<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingRequestReceived extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly Booking $booking)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking request received')
            ->greeting('Booking request received')
            ->line("Room: {$this->booking->service->name}")
            ->line("Guest: {$this->booking->customer_name}")
            ->line('Arrival: '.$this->booking->starts_at->format('M j, Y g:i A'))
            ->line('Status: pending review')
            ->action('Open Aurelia Hotel', route('home'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'status' => $this->booking->status,
        ];
    }
}
