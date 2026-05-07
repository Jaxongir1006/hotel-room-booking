<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancellationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reservation {$this->booking->reference} cancelled",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.bookings.cancellation',
            with: [
                'booking' => $this->booking,
                'room' => $this->booking->room,
                'user' => $this->booking->user,
            ],
        );
    }
}
