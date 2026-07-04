<?php

namespace App\Jobs;

use App\Mail\BookingCancellationMail;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendBookingCancellationEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(public Booking $booking) {}

    public function handle(): void
    {
        $this->booking->loadMissing(['user', 'room']);

        Mail::to($this->booking->user->email)
            ->send(new BookingCancellationMail($this->booking));
    }
}
