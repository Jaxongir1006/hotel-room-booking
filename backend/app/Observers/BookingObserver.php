<?php

namespace App\Observers;

use App\Enums\BookingStatus;
use App\Jobs\GenerateBookingInvoice;
use App\Jobs\SendAdminBookingNotification;
use App\Jobs\SendBookingCancellationEmail;
use App\Jobs\SendBookingConfirmationEmail;
use App\Models\Booking;
use App\Repositories\RoomRepository;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        SendBookingConfirmationEmail::dispatch($booking);
        SendAdminBookingNotification::dispatch($booking);

        RoomRepository::flushShow();
    }

    public function updated(Booking $booking): void
    {
        if ($booking->wasChanged('status')) {
            if ($booking->status === BookingStatus::Cancelled) {
                SendBookingCancellationEmail::dispatch($booking);
            }

            if ($booking->status === BookingStatus::Confirmed) {
                GenerateBookingInvoice::dispatch($booking);
            }

            RoomRepository::flushShow();
        }
    }

    public function deleted(Booking $booking): void
    {
        RoomRepository::flushShow();
    }
}
