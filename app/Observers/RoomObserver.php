<?php

namespace App\Observers;

use App\Models\Room;
use App\Repositories\RoomRepository;

class RoomObserver
{
    public function created(Room $room): void
    {
        $this->flush();
    }

    public function updated(Room $room): void
    {
        $this->flush();
    }

    public function deleted(Room $room): void
    {
        $this->flush();
    }

    public function restored(Room $room): void
    {
        $this->flush();
    }

    public function forceDeleted(Room $room): void
    {
        $this->flush();
    }

    private function flush(): void
    {
        RoomRepository::flushList();
        RoomRepository::flushShow();
    }
}
