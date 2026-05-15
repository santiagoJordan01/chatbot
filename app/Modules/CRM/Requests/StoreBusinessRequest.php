<?php

namespace App\Modules\CRM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', 'alpha_dash', 'unique:businesses,slug'],
            'business_type' => ['required', 'string', 'max:80'],
            'timezone' => ['required', 'string', 'max:100'],
            'settings' => ['nullable', 'array'],
        ];
    }
}
