<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = CarbonImmutable::instance(fake()->dateTimeBetween('-2 months', '+2 months'));
        $nights = fake()->numberBetween(1, 7);
        $checkOut = $checkIn->addDays($nights);
        $pricePerNight = fake()->randomFloat(2, 80, 400);

        return [
            'reference' => 'BK-'.strtoupper(Str::random(10)),
            'user_id' => User::factory(),
            'room_id' => Room::factory(),
            'check_in' => $checkIn->toDateString(),
            'check_out' => $checkOut->toDateString(),
            'nights' => $nights,
            'total_price' => round($pricePerNight * $nights, 2),
            'status' => BookingStatus::Pending,
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => BookingStatus::Pending]);
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => BookingStatus::Confirmed]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => BookingStatus::Cancelled]);
    }

    public function completed(): static
    {
        return $this->state(function () {
            $checkIn = CarbonImmutable::instance(fake()->dateTimeBetween('-3 months', '-1 month'));
            $nights = fake()->numberBetween(1, 5);

            return [
                'status' => BookingStatus::Completed,
                'check_in' => $checkIn->toDateString(),
                'check_out' => $checkIn->addDays($nights)->toDateString(),
                'nights' => $nights,
            ];
        });
    }
}
