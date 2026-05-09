<?php

namespace App\Http\Resources;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Booking
 */
class BookingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'check_in' => $this->check_in?->toDateString(),
            'check_out' => $this->check_out?->toDateString(),
            'nights' => $this->nights,
            'total_price' => (float) $this->total_price,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_color' => $this->status->badgeColor(),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'is_cancellable' => $this->isCancellable(),
            'can_be_reviewed' => $this->canBeReviewed(),
            'room' => $this->whenLoaded('room', fn () => [
                'id' => $this->room->id,
                'name' => $this->room->name,
                'slug' => $this->room->slug,
                'thumbnail' => $this->room->thumbnail,
                'type_label' => $this->room->type->label(),
            ]),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
            'review' => $this->whenLoaded(
                'review',
                fn () => $this->review ? new ReviewResource($this->review) : null
            ),
        ];
    }
}
