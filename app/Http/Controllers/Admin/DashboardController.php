<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\JoinClause;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $now = CarbonImmutable::now();
        $startOfMonth = $now->startOfMonth();
        $startOfPrevMonth = $now->subMonthNoOverflow()->startOfMonth();
        $startOfYear = $now->startOfYear();

        $totals = [
            'rooms' => Room::query()->count(),
            'rooms_available' => Room::query()->where('status', RoomStatus::Available)->count(),
            'users' => User::query()->count(),
            'bookings' => Booking::query()->count(),
            'reviews' => Review::query()->count(),
        ];

        $bookingsByStatus = Booking::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusBreakdown = collect(BookingStatus::cases())
            ->map(fn (BookingStatus $status) => [
                'status' => $status->value,
                'label' => $status->label(),
                'color' => $status->badgeColor(),
                'total' => (int) ($bookingsByStatus[$status->value] ?? 0),
            ])
            ->values();

        $revenue = [
            'this_month' => (float) Booking::query()
                ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Completed])
                ->where('created_at', '>=', $startOfMonth)
                ->sum('total_price'),
            'last_month' => (float) Booking::query()
                ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Completed])
                ->whereBetween('created_at', [$startOfPrevMonth, $startOfMonth])
                ->sum('total_price'),
            'year_to_date' => (float) Booking::query()
                ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Completed])
                ->where('created_at', '>=', $startOfYear)
                ->sum('total_price'),
        ];

        $bookingsTrend = $this->bookingsTrend(days: 30);

        $topRooms = Room::query()
            ->select(['rooms.id', 'rooms.name', 'rooms.slug', 'rooms.thumbnail'])
            ->leftJoin('bookings', function (JoinClause $join) {
                $join->on('bookings.room_id', '=', 'rooms.id')
                    ->whereIn('bookings.status', [
                        BookingStatus::Confirmed->value,
                        BookingStatus::Completed->value,
                    ]);
            })
            ->selectRaw('COUNT(bookings.id) as bookings_count')
            ->selectRaw('COALESCE(SUM(bookings.total_price), 0) as revenue')
            ->groupBy('rooms.id', 'rooms.name', 'rooms.slug', 'rooms.thumbnail')
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get()
            ->map(fn (Room $room) => [
                'id' => $room->id,
                'name' => $room->name,
                'slug' => $room->slug,
                'thumbnail' => $room->thumbnail,
                'bookings_count' => (int) $room->getAttribute('bookings_count'),
                'revenue' => (float) $room->getAttribute('revenue'),
            ]);

        $recentBookings = Booking::query()
            ->with(['room:id,name,slug,thumbnail', 'user:id,name,email'])
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (Booking $booking) => [
                'reference' => $booking->reference,
                'guest' => $booking->user?->name,
                'room' => $booking->room?->name,
                'check_in' => $booking->check_in?->toDateString(),
                'check_out' => $booking->check_out?->toDateString(),
                'total_price' => (float) $booking->total_price,
                'status' => $booking->status->value,
                'status_label' => $booking->status->label(),
                'status_color' => $booking->status->badgeColor(),
                'created_at' => $booking->created_at?->toIso8601String(),
            ]);

        return Inertia::render('Admin/Dashboard', [
            'totals' => $totals,
            'revenue' => $revenue,
            'bookings_by_status' => $statusBreakdown,
            'bookings_trend' => $bookingsTrend,
            'top_rooms' => $topRooms,
            'recent_bookings' => $recentBookings,
        ]);
    }

    /**
     * @return array<int, array{date: string, total: int, revenue: float}>
     */
    protected function bookingsTrend(int $days): array
    {
        $start = CarbonImmutable::now()->subDays($days - 1)->startOfDay();

        $byDay = Booking::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total, COALESCE(SUM(total_price), 0) as revenue')
            ->where('created_at', '>=', $start)
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $trend = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->addDays($i)->toDateString();
            $row = $byDay->get($date);
            $trend[] = [
                'date' => $date,
                'total' => $row ? (int) $row->total : 0,
                'revenue' => $row ? (float) $row->revenue : 0.0,
            ];
        }

        return $trend;
    }
}
