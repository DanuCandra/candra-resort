<?php

namespace App\Http\Requests\Receptionist;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

// Memvalidasi aturan dan periode promosi.
class PromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => Str::upper(trim((string) $this->input('code'))),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $promotion = $this->route('promotion');

        return [
            'code' => ['required', 'string', 'max:100', Rule::unique('promotions')->ignore($promotion)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'discount_type' => ['required', Rule::in(['percent', 'fixed'])],
            'discount_value' => ['required', 'numeric', 'gt:0', Rule::when($this->input('discount_type') === 'percent', ['max:100'])],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'minimum_transaction' => ['required', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'usage_quota' => ['nullable', 'integer', 'min:1'],
            'max_usage_per_guest' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
            'room_types' => ['nullable', 'array'],
            'room_types.*' => ['integer', Rule::exists('room_types', 'id')->whereNull('deleted_at')],
        ];
    }
}
