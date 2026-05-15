<?php

namespace App\Modules\Automation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAutomationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_id' => ['required', 'exists:businesses,id'],
            'name' => ['required', 'string', 'max:150'],
            'trigger_event' => ['required', 'string', 'max:120'],
            'conditions' => ['nullable', 'array'],
            'actions' => ['required', 'array', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
