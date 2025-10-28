<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

 class BookingConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Booking Confirmed')
            ->line('Your booking has been confirmed!')
            ->line('Event: ' . $this->booking->ticket->event->title)
            ->line('Ticket Type: ' . $this->booking->ticket->type)
            ->line('Quantity: ' . $this->booking->quantity)
            ->line('Total Amount: $' . $this->booking->total_amount)
            ->action('View Booking', url('/bookings/' . $this->booking->id))
            ->line('Thank you for your booking!');
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'event_title' => $this->booking->ticket->event->title,
            'ticket_type' => $this->booking->ticket->type,
            'quantity' => $this->booking->quantity,
            'amount' => $this->booking->total_amount,
        ];
    }
}