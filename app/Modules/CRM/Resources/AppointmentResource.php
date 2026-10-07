<?php

namespace App\Modules\CRM\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'lead_id' => $this->lead_id,
            'lead_name' => $this->lead?->name,
            'created_by' => $this->created_by,
            'service_type' => $this->service_type,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'status' => $this->status,
            'notes' => $this->notes,
            'reminder_sent' => $this->reminder_sent,
            'created_at' => $this->created_at,
        ];
    }
}
