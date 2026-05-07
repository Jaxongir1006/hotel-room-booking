<?php

namespace App\Services;

use App\Models\Room;
use App\Repositories\RoomRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class RoomService
{
    public function __construct(private readonly RoomRepository $rooms) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Room>
     */
    public function listForGuests(array $filters, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return $this->rooms->paginateForList($filters, $perPage, $page);
    }

    /**
     * @return EloquentCollection<int, Room>
     */
    public function featured(int $limit = 6): EloquentCollection
    {
        return $this->rooms->featured($limit);
    }

    public function findOrFailBySlug(string $slug): Room
    {
        $room = $this->rooms->findBySlug($slug);

        if ($room === null) {
            abort(404);
        }

        return $room;
    }
}
