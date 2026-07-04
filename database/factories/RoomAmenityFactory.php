<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\RoomAmenity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomAmenity>
 */
class RoomAmenityFactory extends Factory
{
    /** @var array<int, array{name: string, icon: string}> */
    public const POOL = [
        ['name' => 'Free Wi-Fi', 'icon' => 'wifi'],
        ['name' => 'Air Conditioning', 'icon' => 'wind'],
        ['name' => 'Smart TV', 'icon' => 'tv'],
        ['name' => 'Mini Bar', 'icon' => 'wine'],
        ['name' => 'Room Service', 'icon' => 'concierge-bell'],
        ['name' => 'Coffee Maker', 'icon' => 'coffee'],
        ['name' => 'Ocean View', 'icon' => 'waves'],
        ['name' => 'Private Balcony', 'icon' => 'door-open'],
        ['name' => 'Jacuzzi', 'icon' => 'bath'],
        ['name' => 'King Size Bed', 'icon' => 'bed-double'],
        ['name' => 'Workspace', 'icon' => 'briefcase'],
        ['name' => 'Safe', 'icon' => 'lock'],
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amenity = fake()->randomElement(self::POOL);

        return [
            'room_id' => Room::factory(),
            'name' => $amenity['name'],
            'icon' => $amenity['icon'],
        ];
    }
}
