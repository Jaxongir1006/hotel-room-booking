<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $guests = User::where('role', UserRole::Guest)->get();
        $rooms = Room::all();

        if ($guests->isEmpty() || $rooms->isEmpty()) {
            return;
        }

        // 15 completed bookings (with reviews)
        Booking::factory()
            ->count(15)
            ->completed()
            ->state(fn () => [
                'user_id' => $guests->random()->id,
                'room_id' => $rooms->random()->id,
            ])
            ->create()
            ->each(function (Booking $booking) {
                $booking->total_price = round($booking->room->price_per_night * $booking->nights, 2);
                $booking->save();

                Review::factory()->create([
                    'user_id' => $booking->user_id,
                    'room_id' => $booking->room_id,
                    'booking_id' => $booking->id,
                ]);
            });

        // 20 confirmed (future or current)
        Booking::factory()
            ->count(20)
            ->confirmed()
            ->state(function () use ($guests, $rooms) {
                $checkIn = CarbonImmutable::instance(fake()->dateTimeBetween('+1 day', '+2 months'));
                $nights = fake()->numberBetween(1, 7);

                return [
                    'user_id' => $guests->random()->id,
                    'room_id' => $rooms->random()->id,
                    'check_in' => $checkIn->toDateString(),
                    'check_out' => $checkIn->addDays($nights)->toDateString(),
                    'nights' => $nights,
                ];
            })
            ->create()
            ->each(function (Booking $booking) {
                $booking->total_price = round($booking->room->price_per_night * $booking->nights, 2);
                $booking->save();
            });

        // 10 pending
        Booking::factory()
            ->count(10)
            ->pending()
            ->state(function () use ($guests, $rooms) {
                $checkIn = CarbonImmutable::instance(fake()->dateTimeBetween('+1 day', '+2 months'));
                $nights = fake()->numberBetween(1, 5);

                return [
                    'user_id' => $guests->random()->id,
                    'room_id' => $rooms->random()->id,
                    'check_in' => $checkIn->toDateString(),
                    'check_out' => $checkIn->addDays($nights)->toDateString(),
                    'nights' => $nights,
                ];
            })
            ->create()
            ->each(function (Booking $booking) {
                $booking->total_price = round($booking->room->price_per_night * $booking->nights, 2);
                $booking->save();
            });

        // 5 cancelled
        Booking::factory()
            ->count(5)
            ->cancelled()
            ->state(fn () => [
                'user_id' => $guests->random()->id,
                'room_id' => $rooms->random()->id,
            ])
            ->create()
            ->each(function (Booking $booking) {
                $booking->total_price = round($booking->room->price_per_night * $booking->nights, 2);
                $booking->save();
            });
    }
}
