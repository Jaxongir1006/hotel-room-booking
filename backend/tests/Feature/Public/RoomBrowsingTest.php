<?php

namespace Tests\Feature\Public;

use App\Enums\BookingStatus;
use App\Enums\RoomStatus;
use App\Enums\RoomType;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomAmenity;
use App\Models\User;
use App\Repositories\RoomRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RoomBrowsingTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_lists_featured_rooms(): void
    {
        Room::factory()->count(8)->create();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Welcome')
            ->has('featuredRooms.data', 6)
        );
    }

    public function test_room_index_paginates_rooms(): void
    {
        Room::factory()->count(15)->create();

        $response = $this->get('/rooms');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Rooms/Index')
            ->has('rooms.data', 12)
            ->where('rooms.meta.total', 15)
        );
    }

    public function test_filtering_by_type_returns_matching_rooms(): void
    {
        Room::factory()->ofType(RoomType::Suite)->count(3)->create();
        Room::factory()->ofType(RoomType::Single)->count(4)->create();

        $response = $this->get('/rooms?type=suite');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('rooms.meta.total', 3)
            ->where('filters.type', 'suite')
        );
    }

    public function test_filtering_by_min_and_max_price(): void
    {
        Room::factory()->state(['price_per_night' => 50])->create();
        Room::factory()->state(['price_per_night' => 150])->create();
        Room::factory()->state(['price_per_night' => 400])->create();

        $response = $this->get('/rooms?min_price=100&max_price=300');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('rooms.meta.total', 1)
        );
    }

    public function test_search_query_matches_room_name(): void
    {
        Room::factory()->create(['name' => 'Crystal Suite']);
        Room::factory()->create(['name' => 'Royal Deluxe']);

        $response = $this->get('/rooms?q=Crystal');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('rooms.meta.total', 1)
        );
    }

    public function test_unavailable_rooms_are_hidden_from_public_listing(): void
    {
        Room::factory()->count(3)->create();
        Room::factory()->state(['status' => RoomStatus::Unavailable])->create();

        $response = $this->get('/rooms');

        $response->assertInertia(fn ($page) => $page->where('rooms.meta.total', 3));
    }

    public function test_room_show_returns_detail_with_amenities_and_reviews(): void
    {
        $room = Room::factory()->create();
        RoomAmenity::factory()->count(4)->create(['room_id' => $room->id]);

        $response = $this->get("/rooms/{$room->slug}");

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Rooms/Show')
            ->where('room.data.slug', $room->slug)
            ->has('room.data.amenities', 4)
            ->has('room.data.images')
            ->has('room.data.unavailable_dates')
        );
    }

    public function test_unavailable_dates_include_pending_and_confirmed_bookings(): void
    {
        $room = Room::factory()->create();
        $user = User::factory()->create();

        Booking::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'status' => BookingStatus::Confirmed,
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(8)->toDateString(),
            'nights' => 3,
        ]);

        $response = $this->get("/rooms/{$room->slug}");

        $response->assertInertia(fn ($page) => $page->has('room.data.unavailable_dates', 3));
    }

    public function test_observer_flushes_room_cache_on_update(): void
    {
        $room = Room::factory()->create(['name' => 'Original Name']);

        Cache::tags([RoomRepository::TAG_LIST])->put('rooms:list:test', 'cached', 60);
        Cache::tags([RoomRepository::TAG_SHOW])->put("rooms:show:{$room->slug}", 'cached', 60);

        $this->assertSame('cached', Cache::tags([RoomRepository::TAG_LIST])->get('rooms:list:test'));
        $this->assertSame('cached', Cache::tags([RoomRepository::TAG_SHOW])->get("rooms:show:{$room->slug}"));

        $room->update(['name' => 'New Name']);

        $this->assertNull(Cache::tags([RoomRepository::TAG_LIST])->get('rooms:list:test'));
        $this->assertNull(Cache::tags([RoomRepository::TAG_SHOW])->get("rooms:show:{$room->slug}"));
    }
}
