<?php

namespace App\Observers;

use App\Models\Review;
use App\Repositories\RoomRepository;

class ReviewObserver
{
    public function created(Review $review): void
    {
        RoomRepository::flushShow();
        RoomRepository::flushList();
    }

    public function updated(Review $review): void
    {
        RoomRepository::flushShow();
        RoomRepository::flushList();
    }

    public function deleted(Review $review): void
    {
        RoomRepository::flushShow();
        RoomRepository::flushList();
    }
}
