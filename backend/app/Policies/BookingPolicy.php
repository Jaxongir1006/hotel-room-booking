<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id;
    }

    public function create(User $user): bool
    {
        return $user->email_verified_at !== null;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id && $booking->isCancellable();
    }

    public function review(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id && $booking->canBeReviewed();
    }
}
