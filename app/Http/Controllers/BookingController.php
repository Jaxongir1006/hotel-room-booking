<?php

namespace App\Http\Controllers;

use App\Exceptions\RoomNotAvailableException;
use App\Http\Requests\Bookings\StoreRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\RoomDetailResource;
use App\Models\Booking;
use App\Models\Room;
use App\Repositories\BookingRepository;
use App\Services\BookingService;
use App\Services\RoomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookings,
        private readonly BookingRepository $bookingRepo,
        private readonly RoomService $rooms,
    ) {}

    public function create(Request $request): Response
    {
        $slug = $request->string('room')->toString();
        $room = $slug !== '' ? $this->rooms->findOrFailBySlug($slug) : null;

        return Inertia::render('Bookings/Create', [
            'room' => $room ? new RoomDetailResource($room) : null,
        ]);
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $room = Room::query()->where('slug', $request->string('room_slug'))->firstOrFail();
        $this->authorize('create', Booking::class);

        try {
            $booking = $this->bookings->create(
                user: $request->user(),
                room: $room,
                checkIn: $request->string('check_in')->toString(),
                checkOut: $request->string('check_out')->toString(),
                notes: $request->input('notes'),
            );
        } catch (RoomNotAvailableException $e) {
            return back()
                ->withErrors(['room_slug' => $e->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('bookings.show', ['booking' => $booking->reference])
            ->with('toast', [
                'type' => 'success',
                'message' => "Reservation {$booking->reference} received — confirmation email is on its way.",
            ]);
    }

    public function index(Request $request): Response
    {
        $bookings = $this->bookingRepo->paginateForUser($request->user(), 10);

        return Inertia::render('Bookings/Index', [
            'bookings' => BookingResource::collection($bookings),
        ]);
    }

    public function show(Request $request, string $reference): Response
    {
        $booking = $this->bookingRepo->findByReference($reference);

        if ($booking === null) {
            abort(404);
        }

        $this->authorize('view', $booking);

        return Inertia::render('Bookings/Show', [
            'booking' => new BookingResource($booking),
        ]);
    }

    public function cancel(Request $request, string $reference): RedirectResponse
    {
        $booking = $this->bookingRepo->findByReference($reference);

        if ($booking === null) {
            abort(404);
        }

        $this->authorize('cancel', $booking);

        $this->bookings->cancel($booking);

        return redirect()
            ->route('bookings.show', ['booking' => $booking->reference])
            ->with('toast', [
                'type' => 'success',
                'message' => 'Reservation cancelled.',
            ]);
    }
}
