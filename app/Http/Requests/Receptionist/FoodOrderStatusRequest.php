<?php

namespace App\Http\Requests\Receptionist;

use App\Enums\FoodOrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FoodOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(FoodOrderStatus::class)],
        ];
    }
}
