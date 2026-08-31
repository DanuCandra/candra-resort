<?php

namespace App\Http\Requests\Receptionist;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

// Memvalidasi data metode pembayaran.
class PaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $channel = $this->input('channel', 'manual');

        $this->merge([
            'code' => Str::lower(Str::slug((string) ($this->input('code') ?: $this->input('name')), '_')),
            'channel' => $channel,
            'is_online' => $channel === 'midtrans',
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $paymentMethod = $this->route('payment_method');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:100', Rule::unique('payment_methods')->ignore($paymentMethod)],
            'type' => ['required', Rule::in(['cash', 'debit', 'qris', 'bank_transfer', 'card', 'ewallet', 'virtual_account', 'gateway', 'other'])],
            'channel' => ['required', Rule::in(['manual', 'midtrans'])],
            'gateway_method_code' => ['nullable', 'string', 'max:255'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'is_online' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
