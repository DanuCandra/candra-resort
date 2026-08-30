<?php

namespace App\Http\Requests\RoomService;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class FoodOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array'],
            'items.*.quantity' => ['nullable', 'integer', 'min:0', 'max:20'],
            'items.*.special_notes' => ['nullable', 'string', 'max:500'],
            'delivery_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $hasItem = collect($this->input('items', []))->contains(
                fn (array $item): bool => (int) ($item['quantity'] ?? 0) > 0
            );

            if (! $hasItem) {
                $validator->errors()->add('items', 'Pilih minimal satu menu.');
            }
        }];
    }
}
