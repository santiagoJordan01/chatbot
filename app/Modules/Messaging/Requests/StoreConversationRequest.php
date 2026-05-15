<?php

namespace App\Modules\Messaging\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_id' => ['required', 'exists:businesses,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'channel' => ['nullable', 'string', 'max:40'],
            'status' => ['nullable', 'string', 'max:60'],
            'context' => ['nullable', 'array'],
        ];
    }
}
