<?php

namespace App\Modules\Automation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAutomationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:150'],
            'trigger_event' => ['sometimes', 'string', 'max:120'],
            'conditions' => ['nullable', 'array'],
            'actions' => ['sometimes', 'array', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
