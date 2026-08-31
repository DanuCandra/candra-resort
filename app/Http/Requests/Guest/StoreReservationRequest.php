<?php

namespace App\Http\Requests\Guest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Memvalidasi data reservasi online Guest.
class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['promo_code' => $this->filled('promo_code') ? strtoupper(trim((string) $this->input('promo_code'))) : null]);
    }

    public function rules(): array
    {
        return [
            'room_type_id' => ['required', Rule::exists('room_types', 'id')->where(fn ($query) => $query->whereNull('deleted_at')->where('is_active', true))],
            'check_in' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
            'adults' => ['required', 'integer', 'min:1', 'max:50'],
            'children' => ['required', 'integer', 'min:0', 'max:50'],
            'promo_code' => ['nullable', 'string', 'max:100'],
            'special_requests' => ['nullable', 'string', 'max:2000'],
            'terms' => ['accepted'],
        ];
    }
}
