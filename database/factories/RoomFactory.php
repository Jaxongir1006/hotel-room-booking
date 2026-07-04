<?php

namespace Database\Factories;

use App\Enums\RoomStatus;
use App\Enums\RoomType;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(RoomType::cases());
        $name = $this->roomNameFor($type);
        $price = $this->priceFor($type);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->paragraphs(3, true),
            'type' => $type,
            'price_per_night' => $price,
            'capacity' => $this->capacityFor($type),
            'floor' => fake()->numberBetween(1, 8),
            'status' => RoomStatus::Available,
            'thumbnail' => 'https://images.unsplash.com/photo-'.fake()->randomElement([
                '1631049307264-da0ec9d70304',
                '1611892440504-42a792e24d32',
                '1566073771259-6a8506099945',
                '1582719478250-c89cae4dc85b',
                '1590490360182-c33d57733427',
            ]).'?w=1200&q=80',
            'images' => [
                'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=1200&q=80',
                'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=1200&q=80',
                'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80',
            ],
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn () => ['status' => RoomStatus::Unavailable]);
    }

    public function ofType(RoomType $type): static
    {
        return $this->state(fn () => [
            'type' => $type,
            'price_per_night' => $this->priceFor($type),
            'capacity' => $this->capacityFor($type),
            'name' => $this->roomNameFor($type),
        ]);
    }

    private function priceFor(RoomType $type): float
    {
        return match ($type) {
            RoomType::Single => fake()->randomFloat(2, 60, 110),
            RoomType::Double => fake()->randomFloat(2, 110, 180),
            RoomType::Suite => fake()->randomFloat(2, 220, 380),
            RoomType::Deluxe => fake()->randomFloat(2, 380, 650),
        };
    }

    private function capacityFor(RoomType $type): int
    {
        return match ($type) {
            RoomType::Single => 1,
            RoomType::Double => 2,
            RoomType::Suite => 3,
            RoomType::Deluxe => 4,
        };
    }

    private function roomNameFor(RoomType $type): string
    {
        $adjectives = ['Royal', 'Crystal', 'Aurora', 'Imperial', 'Velvet', 'Marble', 'Saffron', 'Azure'];

        return fake()->randomElement($adjectives).' '.$type->label();
    }
}
