<?php

namespace App\Http\Resources\Admin;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Room
 */
class AdminRoomResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'price_per_night' => (float) $this->price_per_night,
            'capacity' => $this->capacity,
            'floor' => $this->floor,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'thumbnail' => $this->thumbnail,
            'images' => $this->images ?? [],
            'amenities' => $this->whenLoaded(
                'amenities',
                fn () => $this->amenities->map(fn ($a) => [
                    'id' => $a->id,
                    'name' => $a->name,
                ])->values()
            ),
            'bookings_count' => $this->whenCounted('bookings'),
            'reviews_count' => $this->whenCounted('reviews'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
