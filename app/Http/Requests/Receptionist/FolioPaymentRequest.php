<?php

namespace App\Http\Requests\Receptionist;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FolioPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_amount' => ['required', 'integer', 'min:1'],
            'payment_method_id' => [
                'required',
                Rule::exists('payment_methods', 'id')->where(fn ($query) => $query
                    ->where('channel', 'manual')->where('is_active', true)->whereNull('deleted_at')),
            ],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
