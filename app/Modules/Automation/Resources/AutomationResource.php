<?php

namespace App\Modules\Automation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AutomationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'name' => $this->name,
            'trigger_event' => $this->trigger_event,
            'conditions' => $this->conditions,
            'actions' => $this->actions,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
