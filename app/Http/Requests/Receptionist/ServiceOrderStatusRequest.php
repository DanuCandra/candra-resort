<?php

namespace App\Http\Requests\Receptionist;

use App\Enums\ServiceOrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceOrderStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return ['status' => ['required', Rule::enum(ServiceOrderStatus::class)], 'scheduled_at' => ['nullable', 'date', 'after:now']];
    }

    public function authorize(): bool
    {
        return true;
    }
}
