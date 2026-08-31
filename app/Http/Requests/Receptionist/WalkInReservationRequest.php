<?php

namespace App\Http\Requests\Receptionist;

use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Memvalidasi reservasi tamu walk-in.
class WalkInReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'guest_phone' => PhoneNumber::normalize($this->input('guest_phone')),
            'promo_code' => $this->filled('promo_code') ? strtoupper(trim((string) $this->input('promo_code'))) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['nullable', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:30'],
            'room_id' => ['required', Rule::exists('rooms', 'id')->where(fn ($query) => $query->whereNull('deleted_at')->where('is_active', true))],
            'check_in' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
            'adults' => ['required', 'integer', 'min:1', 'max:50'],
            'children' => ['required', 'integer', 'min:0', 'max:50'],
            'promo_code' => ['nullable', 'string', 'max:100'],
            'special_requests' => ['nullable', 'string', 'max:2000'],
            'payment_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method_id' => ['nullable', Rule::requiredIf(fn (): bool => (float) $this->input('payment_amount', 0) > 0), Rule::exists('payment_methods', 'id')->where(fn ($query) => $query->whereNull('deleted_at')->where('is_active', true)->where('channel', 'manual'))],
            'reference_number' => ['nullable', 'string', 'max:255'],
        ];
    }
}
