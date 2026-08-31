<?php

namespace App\Http\Requests\Receptionist;

use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Memvalidasi data dan konfirmasi check-in.
class CheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['guest_phone' => PhoneNumber::normalize($this->input('guest_phone'))]);
    }

    public function rules(): array
    {
        return [
            'room_id' => ['required', Rule::exists('rooms', 'id')->where(fn ($query) => $query->whereNull('deleted_at')->where('is_active', true))],
            'guest_phone' => ['required', 'string', 'max:30'],
            'identity_type' => ['required', Rule::in(['KTP', 'SIM', 'Passport', 'Other'])],
            'identity_number' => ['nullable', 'string', 'max:100'],
            'identity_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'key_code' => ['required', 'string', 'max:100'],
            'security_deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method_id' => ['nullable', Rule::requiredIf(fn (): bool => (float) $this->input('payment_amount', 0) > 0), Rule::exists('payment_methods', 'id')->where(fn ($query) => $query->whereNull('deleted_at')->where('is_active', true)->where('channel', 'manual'))],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'confirm_check_in' => ['accepted'],
        ];
    }
}
