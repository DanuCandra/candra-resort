<?php

namespace App\Http\Requests\RoomService;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Memvalidasi permintaan bantuan tamu.
class GuestRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['housekeeping', 'assistance', 'amenity', 'other'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
        ];
    }
}
