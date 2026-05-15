<?php

namespace App\Modules\Messaging\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'lead_id' => $this->lead_id,
            'assigned_to' => $this->assigned_to,
            'channel' => $this->channel,
            'status' => $this->status,
            'last_message_at' => $this->last_message_at,
            'context' => $this->context,
            'created_at' => $this->created_at,
        ];
    }
}
