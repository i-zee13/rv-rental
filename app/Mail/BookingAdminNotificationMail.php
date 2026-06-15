<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingAdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
        $this->booking->loadMissing(['vehicle.translations']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Booking — ' . $this->booking->reference . ' | ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bookings.admin-notification',
        );
    }
}
