<?php

namespace App\Http\Controllers;

use App\Enums\RoomType;
use App\Http\Requests\Rooms\FilterRequest;
use App\Http\Resources\RoomDetailResource;
use App\Http\Resources\RoomResource;
use App\Services\RoomService;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class RoomController extends Controller
{
    public function __construct(private readonly RoomService $rooms) {}

    public function home(): Response
    {
        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
            'featuredRooms' => RoomResource::collection($this->rooms->featured(6)),
        ]);
    }

    public function index(FilterRequest $request): Response
    {
        $filters = $request->filters();
        $page = (int) $request->integer('page', 1);

        $rooms = $this->rooms->listForGuests($filters, perPage: 12, page: $page);

        return Inertia::render('Rooms/Index', [
            'rooms' => RoomResource::collection($rooms),
            'filters' => $filters,
            'roomTypes' => RoomType::options(),
        ]);
    }

    public function show(string $slug): Response
    {
        $room = $this->rooms->findOrFailBySlug($slug);

        return Inertia::render('Rooms/Show', [
            'room' => new RoomDetailResource($room),
        ]);
    }
}
