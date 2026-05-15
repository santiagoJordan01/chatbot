<?php

namespace App\Modules\CRM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assigned_to' => ['sometimes', 'nullable', 'exists:users,id'],
            'name' => ['sometimes', 'string', 'max:150'],
            'phone' => ['sometimes', 'string', 'max:30'],
            'email' => ['sometimes', 'nullable', 'email', 'max:190'],
            'source' => ['sometimes', 'string', 'max:60'],
            'status' => ['sometimes', 'string', 'max:60'],
            'stage' => ['sometimes', 'string', 'max:60'],
            'score' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
