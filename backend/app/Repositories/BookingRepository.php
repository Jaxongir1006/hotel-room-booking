<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookingRepository
{
    /**
     * @return LengthAwarePaginator<int, Booking>
     */
    public function paginateForUser(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return Booking::query()
            ->with('room')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findByReference(string $reference): ?Booking
    {
        return Booking::query()
            ->with(['room.amenities', 'user'])
            ->where('reference', $reference)
            ->first();
    }
}
