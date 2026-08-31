<?php

namespace App\Http\Requests\Staff;

use App\Enums\UserRole;
use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Memvalidasi perubahan profil dan foto staf.
class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(UserRole::Receptionist)
            || $this->user()?->hasRole(UserRole::Owner);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->input('email'))),
            'phone' => PhoneNumber::normalize($this->input('phone')),
            'remove_avatar' => $this->boolean('remove_avatar'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user())],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_avatar' => ['required', 'boolean'],
        ];
    }
}
