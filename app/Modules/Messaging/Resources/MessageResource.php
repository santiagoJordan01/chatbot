<?php

namespace App\Modules\Messaging\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'conversation_id' => $this->conversation_id,
            'sender_user_id' => $this->sender_user_id,
            'direction' => $this->direction,
            'sender_type' => $this->sender_type,
            'content' => $this->content,
            'message_type' => $this->message_type,
            'external_message_id' => $this->external_message_id,
            'sent_at' => $this->sent_at,
            'payload' => $this->payload,
            'created_at' => $this->created_at,
        ];
    }
}
