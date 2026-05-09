<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\RoomStatus;
use App\Http\Resources\BookingResource;
use App\Http\Resources\RoomResource;
use App\Models\Booking;
use App\Models\Room;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $today = CarbonImmutable::now()->startOfDay();

        $stats = [
            'upcoming' => Booking::query()
                ->where('user_id', $user->id)
                ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed])
                ->where('check_in', '>=', $today)
                ->count(),
            'completed' => Booking::query()
                ->where('user_id', $user->id)
                ->where('status', BookingStatus::Completed)
                ->count(),
            'cancelled' => Booking::query()
                ->where('user_id', $user->id)
                ->where('status', BookingStatus::Cancelled)
                ->count(),
            'reviews' => $user->reviews()->count(),
        ];

        $nextStay = Booking::query()
            ->with('room:id,name,slug,thumbnail,type')
            ->where('user_id', $user->id)
            ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed])
            ->where('check_in', '>=', $today)
            ->orderBy('check_in')
            ->first();

        $recent = Booking::query()
            ->with('room:id,name,slug,thumbnail,type')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $featured = Room::query()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('status', RoomStatus::Available)
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('reviews_count')
            ->limit(3)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'nextStay' => $nextStay ? new BookingResource($nextStay) : null,
            'recent' => BookingResource::collection($recent),
            'featured' => RoomResource::collection($featured),
            'isAdmin' => $user->isAdmin(),
        ]);
    }
}
