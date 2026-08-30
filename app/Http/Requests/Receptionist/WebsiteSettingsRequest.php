<?php

namespace App\Http\Requests\Receptionist;

use Illuminate\Foundation\Http\FormRequest;

class WebsiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hotel_name' => ['required', 'string', 'max:150'],
            'hotel_phone' => ['required', 'string', 'max:50'],
            'hotel_email' => ['required', 'email', 'max:255'],
            'hotel_address' => ['required', 'string', 'max:1000'],
            'hotel_tagline' => ['nullable', 'string', 'max:100'],
            'hotel_whatsapp' => ['nullable', 'string', 'max:50'],
            'instagram_url' => ['nullable', 'url', 'max:500'],
            'facebook_url' => ['nullable', 'url', 'max:500'],
            'check_in_time' => ['required', 'date_format:H:i'],
            'check_out_time' => ['required', 'date_format:H:i'],
        ];
    }
}
