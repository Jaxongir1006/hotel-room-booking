<?php

namespace Tests\Feature\Reviews;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_review_their_completed_booking(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $booking = Booking::factory()->completed()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
        ]);

        $response = $this->actingAs($user)->post(
            "/bookings/{$booking->reference}/review",
            ['rating' => 5, 'comment' => 'Stunning suite, would return.']
        );

        $response->assertRedirect("/bookings/{$booking->reference}");
        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'room_id' => $room->id,
            'rating' => 5,
            'comment' => 'Stunning suite, would return.',
        ]);
    }

    public function test_validation_rejects_invalid_rating(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->completed()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post("/bookings/{$booking->reference}/review", ['rating' => 9])
            ->assertSessionHasErrors('rating');

        $this->assertSame(0, Review::query()->count());
    }

    public function test_user_cannot_review_another_users_booking(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $booking = Booking::factory()->completed()->create(['user_id' => $other->id]);

        $this->actingAs($user)
            ->post("/bookings/{$booking->reference}/review", ['rating' => 4])
            ->assertForbidden();
    }

    public function test_user_cannot_review_a_pending_booking(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->pending()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post("/bookings/{$booking->reference}/review", ['rating' => 5])
            ->assertForbidden();
    }

    public function test_user_cannot_review_the_same_booking_twice(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->completed()->create(['user_id' => $user->id]);
        Review::factory()->create([
            'user_id' => $user->id,
            'room_id' => $booking->room_id,
            'booking_id' => $booking->id,
        ]);

        $this->actingAs($user)
            ->post("/bookings/{$booking->reference}/review", ['rating' => 5])
            ->assertForbidden();

        $this->assertSame(1, Review::query()->where('booking_id', $booking->id)->count());
    }

    public function test_admin_can_delete_a_review(): void
    {
        $admin = User::factory()->admin()->create();
        $review = Review::factory()->create();

        $this->actingAs($admin)
            ->delete("/admin/reviews/{$review->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }
}
