<?php

namespace Tests\Feature\Admin;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_aggregated_stats_for_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $rooms = Room::factory()->count(3)->create();

        $confirmed = Booking::factory()->confirmed()->create([
            'room_id' => $rooms[0]->id,
            'total_price' => 500,
        ]);
        $completed1 = Booking::factory()->completed()->create([
            'room_id' => $rooms[0]->id,
            'total_price' => 800,
        ]);
        $completed2 = Booking::factory()->completed()->create([
            'room_id' => $rooms[1]->id,
            'total_price' => 200,
        ]);
        Booking::factory()->cancelled()->create([
            'room_id' => $rooms[1]->id,
        ]);
        Review::factory()->create([
            'room_id' => $rooms[0]->id,
            'booking_id' => $completed1->id,
        ]);
        Review::factory()->create([
            'room_id' => $rooms[1]->id,
            'booking_id' => $completed2->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk()->assertInertia(fn ($page) => $page
            ->component('Admin/Dashboard')
            ->where('totals.rooms', 3)
            ->where('totals.bookings', 4)
            ->where('totals.reviews', 2)
            ->has('bookings_by_status', 4)
            ->has('bookings_trend', 30)
            ->has('top_rooms')
            ->has('recent_bookings', 4)
        );
    }

    public function test_status_breakdown_includes_each_status(): void
    {
        $admin = User::factory()->admin()->create();
        Booking::factory()->pending()->count(2)->create();
        Booking::factory()->confirmed()->count(1)->create();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();

        $statuses = collect(BookingStatus::cases())->map(fn ($s) => $s->value)->all();
        $response->assertInertia(fn ($page) => $page
            ->where('bookings_by_status', function ($breakdown) use ($statuses) {
                $values = collect($breakdown)->pluck('status')->all();
                foreach ($statuses as $status) {
                    if (! in_array($status, $values, true)) {
                        return false;
                    }
                }

                return true;
            })
        );
    }
}
