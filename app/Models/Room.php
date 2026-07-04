<?php

namespace App\Models;

use App\Enums\RoomStatus;
use App\Enums\RoomType;
use App\Observers\RoomObserver;
use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'slug',
    'description',
    'type',
    'price_per_night',
    'capacity',
    'floor',
    'status',
    'thumbnail',
    'images',
])]
#[ObservedBy([RoomObserver::class])]
class Room extends Model
{
    /** @use HasFactory<RoomFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => RoomType::class,
            'status' => RoomStatus::class,
            'price_per_night' => 'decimal:2',
            'images' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return HasMany<RoomAmenity, $this> */
    public function amenities(): HasMany
    {
        return $this->hasMany(RoomAmenity::class);
    }

    /** @return HasMany<Booking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** @return HasMany<Review, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
