<?php

namespace App\Http\Requests\Receptionist;

use App\Enums\GuestRequestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuestRequestStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return ['status' => ['required', Rule::enum(GuestRequestStatus::class)]];
    }

    public function authorize(): bool
    {
        return true;
    }
}
