<?php

namespace App\Repositories;

use App\Enums\BookingStatus;
use App\Enums\RoomStatus;
use App\Models\Booking;
use App\Models\Room;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Pagination\Paginator as PaginatorBase;
use Illuminate\Support\Facades\Cache;

class RoomRepository
{
    public const TAG_LIST = 'rooms:list';

    public const TAG_SHOW = 'rooms:show';

    public const TTL_LIST = 1800; // 30 min

    public const TTL_SHOW = 3600; // 60 min

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Room>
     */
    public function paginateForList(array $filters, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $key = 'rooms:list:'.md5(serialize($filters + ['page' => $page, 'per_page' => $perPage]));

        /** @var array{ids: array<int, int>, total: int} $cached */
        $cached = Cache::tags([self::TAG_LIST])->remember($key, self::TTL_LIST, function () use ($filters, $perPage, $page) {
            $query = $this->buildListQuery($filters);
            $total = (clone $query)->toBase()->getCountForPagination();
            $ids = (clone $query)
                ->forPage($page, $perPage)
                ->pluck('id')
                ->all();

            return ['ids' => $ids, 'total' => $total];
        });

        if (empty($cached['ids'])) {
            $rooms = new EloquentCollection;
        } else {
            $loaded = Room::query()
                ->with('amenities')
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->whereIn('id', $cached['ids'])
                ->get()
                ->keyBy('id');

            /** @var EloquentCollection<int, Room> $rooms */
            $rooms = new EloquentCollection(
                array_values(array_filter(array_map(
                    fn (int $id) => $loaded->get($id),
                    $cached['ids']
                )))
            );
        }

        $paginator = new Paginator(
            $rooms,
            $cached['total'],
            $perPage,
            $page,
            ['path' => PaginatorBase::resolveCurrentPath()]
        );

        return $paginator->appends(request()->query());
    }

    /**
     * @return EloquentCollection<int, Room>
     */
    public function featured(int $limit = 6): EloquentCollection
    {
        $key = 'rooms:featured:'.$limit;

        /** @var EloquentCollection<int, Room> $result */
        $result = Cache::tags([self::TAG_LIST])->remember($key, self::TTL_LIST, function () use ($limit) {
            return Room::query()
                ->with('amenities')
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->where('status', RoomStatus::Available)
                ->orderByDesc('reviews_avg_rating')
                ->orderByDesc('reviews_count')
                ->limit($limit)
                ->get();
        });

        return $result;
    }

    public function findBySlug(string $slug): ?Room
    {
        $key = "rooms:show:{$slug}";

        /** @var Room|null $room */
        $room = Cache::tags([self::TAG_SHOW])->remember($key, self::TTL_SHOW, function () use ($slug) {
            return Room::query()
                ->with(['amenities', 'reviews' => function ($q) {
                    $q->latest()->limit(20)->with('user:id,name');
                }])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->where('slug', $slug)
                ->first();
        });

        if ($room !== null) {
            $room->setAttribute('unavailable_dates', $this->unavailableDates($room->id));
        }

        return $room;
    }

    /**
     * Returns ISO date strings the room is booked across active bookings within the next ~6 months.
     *
     * @return array<int, string>
     */
    public function unavailableDates(int $roomId, int $monthsAhead = 6): array
    {
        $key = "rooms:unavailable:{$roomId}:{$monthsAhead}";

        /** @var array<int, string> $dates */
        $dates = Cache::tags([self::TAG_SHOW])->remember($key, 600, function () use ($roomId, $monthsAhead) {
            $until = CarbonImmutable::now()->addMonths($monthsAhead)->toDateString();

            $bookings = Booking::query()
                ->where('room_id', $roomId)
                ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed])
                ->where('check_out', '>=', CarbonImmutable::now()->toDateString())
                ->where('check_in', '<=', $until)
                ->get(['check_in', 'check_out']);

            $dates = [];
            foreach ($bookings as $booking) {
                $cursor = CarbonImmutable::parse($booking->check_in);
                $end = CarbonImmutable::parse($booking->check_out);
                while ($cursor->lessThan($end)) {
                    $dates[$cursor->toDateString()] = true;
                    $cursor = $cursor->addDay();
                }
            }

            return array_keys($dates);
        });

        return $dates;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function buildListQuery(array $filters): Builder
    {
        $query = Room::query()
            ->with('amenities')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('status', RoomStatus::Available);

        if (! empty($filters['q'])) {
            $term = '%'.$filters['q'].'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['min_price'])) {
            $query->where('price_per_night', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price_per_night', '<=', $filters['max_price']);
        }

        if (! empty($filters['capacity'])) {
            $query->where('capacity', '>=', $filters['capacity']);
        }

        if (! empty($filters['floor'])) {
            $query->where('floor', $filters['floor']);
        }

        if (! empty($filters['amenities']) && is_array($filters['amenities'])) {
            $query->whereHas('amenities', function ($q) use ($filters) {
                $q->whereIn('name', $filters['amenities']);
            }, '>=', count($filters['amenities']));
        }

        match ($filters['sort'] ?? 'newest') {
            'price_asc' => $query->orderBy('price_per_night'),
            'price_desc' => $query->orderByDesc('price_per_night'),
            'rating' => $query->orderByDesc('reviews_avg_rating')->orderByDesc('reviews_count'),
            default => $query->latest(),
        };

        return $query;
    }

    public static function flushList(): void
    {
        Cache::tags([self::TAG_LIST])->flush();
    }

    public static function flushShow(): void
    {
        Cache::tags([self::TAG_SHOW])->flush();
    }
}
