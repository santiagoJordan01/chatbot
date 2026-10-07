<?php

namespace App\Modules\Automation\Services;

use App\Modules\Automation\Models\Automation;
use App\Modules\Messaging\Models\Conversation;
use App\Modules\Messaging\Models\Message;
use Carbon\Carbon;

class AutomationRunner
{
    public function run(int $businessId, string $event, array $context = []): void
    {
        $automations = Automation::query()
            ->where('business_id', $businessId)
            ->where('trigger_event', $event)
            ->where('is_active', true)
            ->get();

        foreach ($automations as $automation) {
            foreach ($automation->actions ?? [] as $action) {
                if (($action['type'] ?? '') !== 'send_message') {
                    continue;
                }

                $this->sendMessage($businessId, $context, (string) ($action['text'] ?? $automation->name));
            }
        }
    }

    protected function sendMessage(int $businessId, array $context, string $text): void
    {
        if ($text === '') {
            return;
        }

        $conversationId = $context['conversation_id'] ?? null;
        $leadId = $context['lead_id'] ?? null;

        if (! $conversationId && $leadId) {
            $conversation = Conversation::query()->firstOrCreate(
                [
                    'business_id' => $businessId,
                    'lead_id' => $leadId,
                    'channel' => 'whatsapp',
                    'status' => 'open',
                ],
                [
                    'context' => ['source' => 'automation'],
                ]
            );
            $conversationId = $conversation->id;
        }

        if (! $conversationId) {
            return;
        }

        Message::query()->create([
            'business_id' => $businessId,
            'conversation_id' => $conversationId,
            'direction' => 'outbound',
            'sender_type' => 'automation',
            'content' => $text,
            'message_type' => 'text',
            'sent_at' => Carbon::now(),
        ]);
    }
}
