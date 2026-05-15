<?php

namespace App\Modules\CRM\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'assigned_to' => $this->assigned_to,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'source' => $this->source,
            'status' => $this->status,
            'stage' => $this->stage,
            'score' => $this->score,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
        ];
    }
}
