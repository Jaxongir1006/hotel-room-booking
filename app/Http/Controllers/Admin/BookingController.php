<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function index(Request $request): Response
    {
        $bookings = Booking::query()
            ->with(['room:id,name,slug,thumbnail,type', 'user:id,name,email'])
            ->when(
                $request->string('status')->toString(),
                fn ($q, string $status) => $q->where('status', $status)
            )
            ->when(
                $request->string('q')->toString(),
                fn ($q, string $term) => $q->where(function ($inner) use ($term) {
                    $inner->where('reference', 'like', "%{$term}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"))
                        ->orWhereHas('room', fn ($r) => $r->where('name', 'like', "%{$term}%"));
                })
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => BookingResource::collection($bookings),
            'filters' => [
                'q' => $request->string('q')->toString() ?: null,
                'status' => $request->string('status')->toString() ?: null,
            ],
            'statuses' => collect(BookingStatus::cases())
                ->map(fn (BookingStatus $s) => ['value' => $s->value, 'label' => $s->label()])
                ->values(),
        ]);
    }

    public function show(Booking $booking): Response
    {
        $booking->load(['room.amenities', 'user', 'review.user:id,name']);

        return Inertia::render('Admin/Bookings/Show', [
            'booking' => new BookingResource($booking),
            'statuses' => collect(BookingStatus::cases())
                ->map(fn (BookingStatus $s) => ['value' => $s->value, 'label' => $s->label()])
                ->values(),
        ]);
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::enum(BookingStatus::class)],
        ]);

        $booking->update(['status' => $data['status']]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Booking {$booking->reference} marked as {$booking->status->label()}.",
        ]);
    }
}
