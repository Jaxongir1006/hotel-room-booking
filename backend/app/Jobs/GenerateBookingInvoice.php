<?php

namespace App\Jobs;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateBookingInvoice implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public Booking $booking) {}

    public function handle(): void
    {
        $this->booking->loadMissing(['user', 'room']);

        $pdf = Pdf::loadView('invoices.booking', [
            'booking' => $this->booking,
            'room' => $this->booking->room,
            'user' => $this->booking->user,
        ]);

        $path = "invoices/{$this->booking->reference}.pdf";

        Storage::disk('local')->put($path, $pdf->output());
    }
}
