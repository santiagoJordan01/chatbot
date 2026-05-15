<?php

namespace App\Modules\Messaging\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lead_id' => ['sometimes', 'nullable', 'exists:leads,id'],
            'assigned_to' => ['sometimes', 'nullable', 'exists:users,id'],
            'channel' => ['sometimes', 'string', 'max:40'],
            'status' => ['sometimes', 'string', 'max:60'],
            'context' => ['nullable', 'array'],
        ];
    }
}
