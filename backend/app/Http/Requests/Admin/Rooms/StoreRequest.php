<?php

namespace App\Http\Requests\Admin\Rooms;

use App\Enums\RoomStatus;
use App\Enums\RoomType;
use App\Models\Room;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Room::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:5000'],
            'type' => ['required', Rule::enum(RoomType::class)],
            'price_per_night' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'floor' => ['required', 'integer', 'min:0', 'max:200'],
            'status' => ['required', Rule::enum(RoomStatus::class)],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'amenities' => ['nullable', 'array', 'max:30'],
            'amenities.*' => ['string', 'max:80'],
        ];
    }
}
