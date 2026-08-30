<?php

namespace App\Http\Requests\RoomService;

use Illuminate\Foundation\Http\FormRequest;

class ServiceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hotel_service_id' => ['required', 'integer', 'exists:hotel_services,id'],
            'quantity' => ['required', 'numeric', 'min:0.1', 'max:100'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
