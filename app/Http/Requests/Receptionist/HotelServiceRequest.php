<?php

namespace App\Http\Requests\Receptionist;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

// Memvalidasi data layanan hotel.
class HotelServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => Str::upper(Str::slug($this->input('code') ?: $this->input('name'), '-')),
            'requires_schedule' => $this->boolean('requires_schedule'),
            'is_available' => $this->boolean('is_available'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:100', Rule::unique('hotel_services')->ignore($this->route('hotel_service'))],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', Rule::in(['massage', 'spa', 'laundry', 'transport', 'extra_bed', 'other'])],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
            'price_unit' => ['required', Rule::in(['per_order', 'per_hour', 'per_item', 'per_kg'])],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:10080'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'requires_schedule' => ['required', 'boolean'],
            'is_available' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
