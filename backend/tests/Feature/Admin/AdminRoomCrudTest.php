<?php

namespace Tests\Feature\Admin;

use App\Enums\RoomStatus;
use App\Enums\RoomType;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminRoomCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_room_with_thumbnail_and_amenities(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/rooms', [
            'name' => 'Aurelia Suite',
            'description' => 'A spacious suite with skyline views.',
            'type' => RoomType::Suite->value,
            'price_per_night' => 350.50,
            'capacity' => 3,
            'floor' => 8,
            'status' => RoomStatus::Available->value,
            'thumbnail' => UploadedFile::fake()->image('thumb.jpg'),
            'images' => [
                UploadedFile::fake()->image('a.jpg'),
                UploadedFile::fake()->image('b.jpg'),
            ],
            'amenities' => ['WiFi', 'Mini bar', 'WiFi'],
        ]);

        $response->assertRedirect('/admin/rooms');

        $room = Room::query()->where('name', 'Aurelia Suite')->firstOrFail();
        $this->assertSame('aurelia-suite', $room->slug);
        $this->assertNotNull($room->thumbnail);
        Storage::disk('public')->assertExists($room->thumbnail);
        $this->assertCount(2, $room->images);
        $this->assertSame(2, $room->amenities()->count());
    }

    public function test_admin_can_update_a_room_and_replace_thumbnail(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $oldThumbnail = UploadedFile::fake()->image('old.jpg')->store('rooms', 'public');
        $room = Room::factory()->create([
            'thumbnail' => $oldThumbnail,
            'images' => ['rooms/keep.jpg'],
        ]);
        Storage::disk('public')->put('rooms/keep.jpg', 'placeholder');

        $response = $this->actingAs($admin)->put("/admin/rooms/{$room->slug}", [
            'name' => 'Renamed Room',
            'description' => 'Updated description.',
            'type' => RoomType::Deluxe->value,
            'price_per_night' => 199.99,
            'capacity' => 2,
            'floor' => 4,
            'status' => RoomStatus::Available->value,
            'thumbnail' => UploadedFile::fake()->image('new.jpg'),
            'kept_images' => ['rooms/keep.jpg'],
            'amenities' => ['Spa'],
        ]);

        $response->assertRedirect('/admin/rooms');
        $room->refresh();
        $this->assertSame('Renamed Room', $room->name);
        $this->assertSame('renamed-room', $room->slug);
        Storage::disk('public')->assertMissing($oldThumbnail);
        Storage::disk('public')->assertExists($room->thumbnail);
        $this->assertSame(['rooms/keep.jpg'], $room->images);
    }

    public function test_admin_can_delete_a_room_and_files(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        Storage::disk('public')->put('rooms/thumb.jpg', 'x');
        Storage::disk('public')->put('rooms/gallery.jpg', 'x');
        $room = Room::factory()->create([
            'thumbnail' => 'rooms/thumb.jpg',
            'images' => ['rooms/gallery.jpg'],
        ]);

        $this->actingAs($admin)
            ->delete("/admin/rooms/{$room->slug}")
            ->assertRedirect('/admin/rooms');

        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
        Storage::disk('public')->assertMissing('rooms/thumb.jpg');
        Storage::disk('public')->assertMissing('rooms/gallery.jpg');
    }

    public function test_non_admin_cannot_create_a_room(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/admin/rooms', ['name' => 'Hack'])
            ->assertForbidden();
    }
}
