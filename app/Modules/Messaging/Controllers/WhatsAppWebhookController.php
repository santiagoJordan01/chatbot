<?php

namespace App\Modules\Messaging\Controllers;

use App\Modules\Messaging\Jobs\ProcessIncomingMessageJob;
use App\Shared\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WhatsAppWebhookController extends ApiController
{
    public function incoming(Request $request): JsonResponse
    {
        $secret = config('services.whatsapp.webhook_secret');

        if ($secret && ! hash_equals($secret, (string) $request->header('X-Webhook-Secret', ''))) {
            return $this->error('Unauthorized webhook request', status: Response::HTTP_UNAUTHORIZED);
        }

        $validated = $request->validate([
            'business_id' => ['required', 'integer', 'exists:businesses,id'],
            'lead_id' => ['nullable', 'integer', 'exists:leads,id'],
            'content' => ['nullable', 'string'],
            'message_type' => ['nullable', 'string', 'max:60'],
            'external_message_id' => ['nullable', 'string', 'max:190'],
            'provider' => ['nullable', 'string', 'max:40'],
            'meta' => ['nullable', 'array'],
        ]);

        ProcessIncomingMessageJob::dispatch($validated)->onQueue('whatsapp');

        return $this->success([
            'queued' => true,
        ], 'Incoming WhatsApp message queued', 202);
    }
}
