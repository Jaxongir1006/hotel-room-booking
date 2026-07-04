<?php

namespace App\Http\Requests\Rooms;

use App\Enums\RoomType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:120'],
            'type' => ['nullable', Rule::enum(RoomType::class)],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'gte:min_price'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:10'],
            'floor' => ['nullable', 'integer', 'min:1', 'max:50'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:60'],
            'sort' => ['nullable', Rule::in(['price_asc', 'price_desc', 'newest', 'rating'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string|int|array<int, string>|null>
     */
    public function filters(): array
    {
        return [
            'q' => $this->string('q')->trim()->toString() ?: null,
            'type' => $this->input('type'),
            'min_price' => $this->input('min_price'),
            'max_price' => $this->input('max_price'),
            'capacity' => $this->input('capacity'),
            'floor' => $this->input('floor'),
            'amenities' => $this->input('amenities', []),
            'sort' => $this->input('sort', 'newest'),
        ];
    }
}
