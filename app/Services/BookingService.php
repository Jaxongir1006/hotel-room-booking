<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\RoomStatus;
use App\Exceptions\RoomNotAvailableException;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    /**
     * @return array{nights: int, total: float}
     */
    public function quote(Room $room, string $checkIn, string $checkOut): array
    {
        $nights = (int) CarbonImmutable::parse($checkIn)->diffInDays(CarbonImmutable::parse($checkOut));

        return [
            'nights' => $nights,
            'total' => round((float) $room->price_per_night * $nights, 2),
        ];
    }

    /**
     * @throws RoomNotAvailableException
     */
    public function create(
        User $user,
        Room $room,
        string $checkIn,
        string $checkOut,
        ?string $notes = null,
    ): Booking {
        if ($room->status !== RoomStatus::Available) {
            throw new RoomNotAvailableException('This room is not currently available.');
        }

        return DB::transaction(function () use ($user, $room, $checkIn, $checkOut, $notes) {
            $overlap = Booking::query()
                ->where('room_id', $room->id)
                ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed])
                ->overlapping($checkIn, $checkOut)
                ->lockForUpdate()
                ->exists();

            if ($overlap) {
                throw new RoomNotAvailableException('The room is already booked for the selected dates.');
            }

            $quote = $this->quote($room, $checkIn, $checkOut);

            return Booking::create([
                'reference' => $this->generateReference(),
                'user_id' => $user->id,
                'room_id' => $room->id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'nights' => $quote['nights'],
                'total_price' => $quote['total'],
                'status' => BookingStatus::Pending,
                'notes' => $notes,
            ]);
        });
    }

    public function cancel(Booking $booking): Booking
    {
        $booking->status = BookingStatus::Cancelled;
        $booking->save();

        return $booking;
    }

    private function generateReference(): string
    {
        do {
            $reference = 'BK-'.strtoupper(Str::random(10));
        } while (Booking::where('reference', $reference)->exists());

        return $reference;
    }
}
