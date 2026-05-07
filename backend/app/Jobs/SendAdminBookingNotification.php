<?php

namespace App\Jobs;

use App\Enums\UserRole;
use App\Mail\AdminBookingNotificationMail;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendAdminBookingNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(public Booking $booking) {}

    public function handle(): void
    {
        $this->booking->loadMissing(['user', 'room']);

        $admins = User::query()
            ->where('role', UserRole::Admin)
            ->pluck('email')
            ->all();

        if (empty($admins)) {
            return;
        }

        Mail::to($admins)->send(new AdminBookingNotificationMail($this->booking));
    }
}
