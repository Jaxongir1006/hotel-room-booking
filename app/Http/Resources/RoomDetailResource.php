<?php

namespace App\Http\Resources;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Room
 */
class RoomDetailResource extends JsonResource
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
            'thumbnail' => $this->thumbnail,
            'images' => $this->images ?? [],
            'average_rating' => $this->resource->average_rating !== null
                ? round((float) $this->resource->average_rating, 1)
                : null,
            'reviews_count' => (int) ($this->resource->reviews_count ?? 0),
            'amenities' => RoomAmenityResource::collection($this->whenLoaded('amenities')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'unavailable_dates' => $this->resource->unavailable_dates ?? [],
        ];
    }
}
