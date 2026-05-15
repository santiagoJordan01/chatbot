<?php

namespace App\Modules\Messaging\Jobs;

use App\Modules\Messaging\Events\IncomingMessageReceived;
use App\Modules\Messaging\Services\WhatsAppWebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessIncomingMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function __construct(private readonly array $payload)
    {
    }

    public function handle(WhatsAppWebhookService $service): void
    {
        $message = $service->persistIncomingMessage($this->payload);

        IncomingMessageReceived::dispatch($message);
    }
}
