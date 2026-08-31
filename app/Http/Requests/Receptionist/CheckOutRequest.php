<?php

namespace App\Http\Requests\Receptionist;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Memvalidasi pembayaran dan konfirmasi check-out.
class CheckOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method_id' => ['nullable', Rule::requiredIf(fn (): bool => (float) $this->input('payment_amount', 0) > 0), Rule::exists('payment_methods', 'id')->where(fn ($query) => $query->whereNull('deleted_at')->where('is_active', true)->where('channel', 'manual'))],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'key_returned' => ['accepted'],
            'confirm_check_out' => ['accepted'],
        ];
    }
}
