<?php

namespace Database\Seeders;

use App\Enums\RoomType;
use App\Models\Room;
use App\Models\RoomAmenity;
use Database\Factories\RoomAmenityFactory;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $distribution = [
            [RoomType::Single, 5],
            [RoomType::Double, 7],
            [RoomType::Suite, 5],
            [RoomType::Deluxe, 3],
        ];

        foreach ($distribution as [$type, $count]) {
            Room::factory()
                ->count($count)
                ->ofType($type)
                ->create()
                ->each(function (Room $room) {
                    $picked = collect(RoomAmenityFactory::POOL)
                        ->shuffle()
                        ->take(fake()->numberBetween(4, 8));

                    foreach ($picked as $amenity) {
                        RoomAmenity::create([
                            'room_id' => $room->id,
                            'name' => $amenity['name'],
                            'icon' => $amenity['icon'],
                        ]);
                    }
                });
        }
    }
}
