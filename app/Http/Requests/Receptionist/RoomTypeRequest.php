<?php

namespace App\Http\Requests\Receptionist;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

// Memvalidasi data lengkap tipe kamar.
class RoomTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => Str::upper(trim((string) $this->input('code'))),
            'slug' => Str::slug($this->input('slug') ?: $this->input('name')),
            'breakfast_included' => $this->boolean('breakfast_included'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $roomType = $this->route('room_type');

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('room_types')->ignore($roomType)],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('room_types')->ignore($roomType)],
            'description' => ['nullable', 'string'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'max_adults' => ['required', 'integer', 'min:1', 'max:50'],
            'max_children' => ['required', 'integer', 'min:0', 'max:50'],
            'bed_type' => ['nullable', 'string', 'max:100'],
            'bed_count' => ['required', 'integer', 'min:1', 'max:20'],
            'room_size_sqm' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'extra_bed_price' => ['required', 'numeric', 'min:0'],
            'breakfast_included' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['integer', Rule::exists('facilities', 'id')->whereNull('deleted_at')],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
