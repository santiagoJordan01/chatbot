<?php

namespace App\Modules\Messaging\Controllers;

use App\Modules\Messaging\Models\Conversation;
use App\Modules\Messaging\Requests\StoreConversationRequest;
use App\Modules\Messaging\Requests\UpdateConversationRequest;
use App\Modules\Messaging\Resources\ConversationResource;
use App\Shared\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Conversation::class);

        $businessId = (int) $request->integer('business_id', $request->user()?->business_id ?? 0);

        $conversations = Conversation::query()
            ->where('business_id', $businessId)
            ->latest('last_message_at')
            ->paginate((int) $request->integer('per_page', 15));

        return $this->success(ConversationResource::collection($conversations), 'Conversations retrieved');
    }

    public function store(StoreConversationRequest $request): JsonResponse
    {
        $this->authorize('create', Conversation::class);

        $conversation = Conversation::query()->create($request->validated());

        return $this->success(new ConversationResource($conversation), 'Conversation created', 201);
    }

    public function show(Conversation $conversation): JsonResponse
    {
        $this->authorize('view', $conversation);

        return $this->success(new ConversationResource($conversation), 'Conversation retrieved');
    }

    public function update(UpdateConversationRequest $request, Conversation $conversation): JsonResponse
    {
        $this->authorize('update', $conversation);

        $conversation->update($request->validated());

        return $this->success(new ConversationResource($conversation->refresh()), 'Conversation updated');
    }

    public function destroy(Conversation $conversation): JsonResponse
    {
        $this->authorize('delete', $conversation);

        $conversation->delete();

        return $this->success(null, 'Conversation deleted');
    }
}
