<?php

namespace App\Modules\CRM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_id' => ['required', 'exists:businesses,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:190'],
            'source' => ['nullable', 'string', 'max:60'],
            'status' => ['nullable', 'string', 'max:60'],
            'stage' => ['nullable', 'string', 'max:60'],
            'score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
