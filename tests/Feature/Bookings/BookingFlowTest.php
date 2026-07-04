<?php

namespace Tests\Feature\Bookings;

use App\Enums\BookingStatus;
use App\Jobs\GenerateBookingInvoice;
use App\Jobs\SendAdminBookingNotification;
use App\Jobs\SendBookingCancellationEmail;
use App\Jobs\SendBookingConfirmationEmail;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_page_loads_with_room_when_slug_present(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        $this->actingAs($user)
            ->get('/bookings/create?room='.$room->slug)
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Bookings/Create')
                ->where('room.data.slug', $room->slug)
            );
    }

    public function test_authenticated_user_can_create_a_booking(): void
    {
        Queue::fake();
        $user = User::factory()->create();
        $room = Room::factory()->create(['price_per_night' => 100]);

        $payload = [
            'room_slug' => $room->slug,
            'check_in' => now()->addDays(3)->toDateString(),
            'check_out' => now()->addDays(6)->toDateString(),
            'notes' => 'Late arrival around 10pm',
        ];

        $response = $this->actingAs($user)->post('/bookings', $payload);

        $booking = Booking::where('user_id', $user->id)->latest('id')->first();
        $this->assertNotNull($booking);
        $this->assertSame(3, $booking->nights);
        $this->assertSame('300.00', $booking->total_price);
        $this->assertSame(BookingStatus::Pending, $booking->status);

        $response->assertRedirect("/bookings/{$booking->reference}");

        Queue::assertPushed(SendBookingConfirmationEmail::class);
        Queue::assertPushed(SendAdminBookingNotification::class);
    }

    public function test_overlapping_booking_is_rejected(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $room = Room::factory()->create(['price_per_night' => 100]);

        Booking::factory()->confirmed()->create([
            'user_id' => $other->id,
            'room_id' => $room->id,
            'check_in' => now()->addDays(2)->toDateString(),
            'check_out' => now()->addDays(5)->toDateString(),
            'nights' => 3,
        ]);

        $response = $this->actingAs($user)->post('/bookings', [
            'room_slug' => $room->slug,
            'check_in' => now()->addDays(3)->toDateString(),
            'check_out' => now()->addDays(6)->toDateString(),
        ]);

        $response->assertSessionHasErrors('room_slug');
        $this->assertSame(1, Booking::where('room_id', $room->id)->count());
    }

    public function test_validation_rejects_check_out_not_after_check_in(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        $response = $this->actingAs($user)->post('/bookings', [
            'room_slug' => $room->slug,
            'check_in' => now()->addDays(3)->toDateString(),
            'check_out' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertSessionHasErrors('check_out');
    }

    public function test_index_lists_only_authenticated_users_bookings(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Booking::factory()->count(3)->create(['user_id' => $user->id]);
        Booking::factory()->count(2)->create(['user_id' => $other->id]);

        $this->actingAs($user)
            ->get('/bookings')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('bookings.meta.total', 3));
    }

    public function test_user_cannot_view_other_users_booking(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $other->id]);

        $this->actingAs($user)
            ->get("/bookings/{$booking->reference}")
            ->assertForbidden();
    }

    public function test_admin_can_view_any_booking(): void
    {
        $admin = User::factory()->admin()->create();
        $booking = Booking::factory()->create();

        $this->actingAs($admin)
            ->get("/bookings/{$booking->reference}")
            ->assertOk();
    }

    public function test_user_can_cancel_pending_booking(): void
    {
        Queue::fake();
        $user = User::factory()->create();
        $booking = Booking::factory()->pending()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post("/bookings/{$booking->reference}/cancel")
            ->assertRedirect("/bookings/{$booking->reference}");

        $this->assertSame(BookingStatus::Cancelled, $booking->fresh()->status);
        Queue::assertPushed(SendBookingCancellationEmail::class);
    }

    public function test_user_cannot_cancel_confirmed_booking(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->confirmed()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post("/bookings/{$booking->reference}/cancel")
            ->assertForbidden();

        $this->assertSame(BookingStatus::Confirmed, $booking->fresh()->status);
    }

    public function test_invoice_job_is_dispatched_when_booking_status_changes_to_confirmed(): void
    {
        Queue::fake();
        $booking = Booking::factory()->pending()->create();

        $booking->update(['status' => BookingStatus::Confirmed]);

        Queue::assertPushed(GenerateBookingInvoice::class);
    }

    public function test_store_endpoint_is_rate_limited(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($user)->post('/bookings', [
                'room_slug' => $room->slug,
                'check_in' => now()->addDays(2 + $i * 5)->toDateString(),
                'check_out' => now()->addDays(3 + $i * 5)->toDateString(),
            ]);
        }

        $this->actingAs($user)
            ->post('/bookings', [
                'room_slug' => $room->slug,
                'check_in' => now()->addDays(60)->toDateString(),
                'check_out' => now()->addDays(61)->toDateString(),
            ])
            ->assertStatus(429);
    }
}
