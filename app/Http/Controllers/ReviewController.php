<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reviews\StoreRequest;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(StoreRequest $request, Booking $booking): RedirectResponse
    {
        $this->authorize('create', [Review::class, $booking]);

        Review::create([
            'user_id' => $request->user()->id,
            'room_id' => $booking->room_id,
            'booking_id' => $booking->id,
            'rating' => $request->integer('rating'),
            'comment' => $request->string('comment')->trim()->value() ?: null,
        ]);

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Thank you for sharing your experience.');
    }
}
