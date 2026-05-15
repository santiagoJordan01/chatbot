<?php

namespace App\Modules\CRM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_type' => ['sometimes', 'string', 'max:120'],
            'starts_at' => ['sometimes', 'date'],
            'ends_at' => ['nullable', 'date'],
            'status' => ['sometimes', 'string', 'max:60'],
            'notes' => ['nullable', 'string'],
            'reminder_sent' => ['sometimes', 'boolean'],
        ];
    }
}
