<?php

namespace App\Http\Requests\Receptionist;

use App\Enums\RoomStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'room_number' => trim((string) $this->input('room_number')),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $room = $this->route('room');

        return [
            'room_type_id' => ['required', Rule::exists('room_types', 'id')->whereNull('deleted_at')],
            'room_number' => ['required', 'string', 'max:50', Rule::unique('rooms')->ignore($room)],
            'floor' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::enum(RoomStatus::class)],
            'status_reason' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
