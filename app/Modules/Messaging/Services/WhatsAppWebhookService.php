<?php

namespace App\Modules\Messaging\Services;

use App\Modules\Messaging\Models\Conversation;
use App\Modules\Messaging\Models\Message;
use Carbon\Carbon;

class WhatsAppWebhookService
{
    public function persistIncomingMessage(array $payload): Message
    {
        $conversation = Conversation::query()->firstOrCreate(
            [
                'business_id' => $payload['business_id'],
                'lead_id' => $payload['lead_id'] ?? null,
                'channel' => 'whatsapp',
                'status' => 'open',
            ],
            [
                'assigned_to' => null,
                'context' => ['source' => 'whatsapp_webhook'],
            ]
        );

        $conversation->forceFill([
            'last_message_at' => Carbon::now(),
        ])->save();

        return Message::query()->create([
            'business_id' => $payload['business_id'],
            'conversation_id' => $conversation->id,
            'direction' => 'inbound',
            'sender_type' => 'contact',
            'content' => $payload['content'] ?? null,
            'message_type' => $payload['message_type'] ?? 'text',
            'external_message_id' => $payload['external_message_id'] ?? null,
            'payload' => $payload,
            'sent_at' => Carbon::now(),
        ]);
    }
}
