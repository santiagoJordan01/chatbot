<?php

namespace App\Modules\Messaging\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_id' => ['required', 'exists:businesses,id'],
            'conversation_id' => ['required', 'exists:conversations,id'],
            'sender_user_id' => ['nullable', 'exists:users,id'],
            'direction' => ['required', 'in:inbound,outbound'],
            'sender_type' => ['nullable', 'string', 'max:60'],
            'content' => ['nullable', 'string'],
            'message_type' => ['nullable', 'string', 'max:60'],
            'external_message_id' => ['nullable', 'string', 'max:190'],
            'sent_at' => ['nullable', 'date'],
            'payload' => ['nullable', 'array'],
        ];
    }
}
