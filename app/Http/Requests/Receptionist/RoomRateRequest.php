<?php

namespace App\Http\Requests\Receptionist;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Memvalidasi data tarif kamar.
class RoomRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }

    public function rules(): array
    {
        return [
            'room_type_id' => ['required', Rule::exists('room_types', 'id')->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'days_of_week' => ['nullable', 'array'],
            'days_of_week.*' => ['integer', 'between:1,7'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'priority' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
