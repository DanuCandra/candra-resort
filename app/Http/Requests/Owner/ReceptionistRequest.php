<?php

namespace App\Http\Requests\Owner;

use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ReceptionistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => PhoneNumber::normalize($this->input('phone')),
            'username' => strtolower(trim((string) $this->input('username'))),
            'employee_code' => strtoupper(trim((string) $this->input('employee_code'))),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $receptionist = $this->route('receptionist');
        $creating = $receptionist === null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($receptionist)],
            'username' => ['required', 'alpha_dash:ascii', 'max:255', Rule::unique('users')->ignore($receptionist)],
            'employee_code' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($receptionist)],
            'phone' => ['required', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'address' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['required', 'boolean'],
            'password' => [$creating ? 'required' : 'nullable', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }
}
