<?php

namespace App\Modules\Messaging\Controllers;

use App\Modules\Messaging\Models\Message;
use App\Modules\Messaging\Requests\StoreMessageRequest;
use App\Modules\Messaging\Resources\MessageResource;
use App\Shared\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Message::class);

        $businessId = (int) $request->integer('business_id', $request->user()?->business_id ?? 0);
        $conversationId = (int) $request->integer('conversation_id');

        $messages = Message::query()
            ->where('business_id', $businessId)
            ->when($conversationId > 0, fn ($query) => $query->where('conversation_id', $conversationId))
            ->latest('created_at')
            ->paginate((int) $request->integer('per_page', 50));

        return $this->success(MessageResource::collection($messages), 'Messages retrieved');
    }

    public function store(StoreMessageRequest $request): JsonResponse
    {
        $this->authorize('create', Message::class);

        $message = Message::query()->create($request->validated());

        return $this->success(new MessageResource($message), 'Message created', 201);
    }
}
