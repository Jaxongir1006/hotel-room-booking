<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function index(Request $request): Response
    {
        $reviews = Review::query()
            ->with(['user:id,name,email', 'room:id,name,slug,thumbnail', 'booking:id,reference'])
            ->when(
                $request->string('q')->toString(),
                fn ($q, string $term) => $q->where(function ($inner) use ($term) {
                    $inner->where('comment', 'like', "%{$term}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('room', fn ($r) => $r->where('name', 'like', "%{$term}%"));
                })
            )
            ->when(
                $request->integer('rating'),
                fn ($q, int $rating) => $q->where('rating', $rating)
            )
            ->latest()
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Review $review) => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at?->toIso8601String(),
                'guest_name' => $review->user?->name,
                'guest_email' => $review->user?->email,
                'room' => $review->room ? [
                    'id' => $review->room->id,
                    'name' => $review->room->name,
                    'slug' => $review->room->slug,
                    'thumbnail' => $review->room->thumbnail,
                ] : null,
                'booking_reference' => $review->booking?->reference,
            ]);

        return Inertia::render('Admin/Reviews/Index', [
            'reviews' => $reviews,
            'filters' => [
                'q' => $request->string('q')->toString() ?: null,
                'rating' => $request->integer('rating') ?: null,
            ],
        ]);
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Review removed.',
        ]);
    }
}
