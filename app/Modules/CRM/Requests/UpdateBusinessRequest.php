<?php

namespace App\Modules\CRM\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $businessId = $this->route('business')?->id;

        return [
            'name' => ['sometimes', 'string', 'max:150'],
            'slug' => ['sometimes', 'string', 'max:150', 'alpha_dash', Rule::unique('businesses', 'slug')->ignore($businessId)],
            'business_type' => ['sometimes', 'string', 'max:80'],
            'timezone' => ['sometimes', 'string', 'max:100'],
            'settings' => ['nullable', 'array'],
        ];
    }
}
