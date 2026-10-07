<?php

namespace App\Modules\Automation\Listeners;

use App\Modules\Automation\Services\AutomationRunner;
use App\Modules\Messaging\Events\IncomingMessageReceived;

class RunMessageAutomations
{
    public function __construct(private readonly AutomationRunner $runner)
    {
    }

    public function handle(IncomingMessageReceived $event): void
    {
        $message = $event->message->loadMissing('conversation');

        $this->runner->run($message->business_id, 'message.received', [
            'conversation_id' => $message->conversation_id,
            'lead_id' => $message->conversation?->lead_id,
        ]);
    }
}
