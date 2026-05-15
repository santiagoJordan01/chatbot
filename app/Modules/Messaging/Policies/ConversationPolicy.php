<?php

namespace App\Modules\Messaging\Policies;

use App\Models\User;
use App\Modules\Messaging\Models\Conversation;

class ConversationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->business_id !== null;
    }

    public function view(User $user, Conversation $conversation): bool
    {
        return $user->business_id === $conversation->business_id;
    }

    public function create(User $user): bool
    {
        return $user->business_id !== null;
    }

    public function update(User $user, Conversation $conversation): bool
    {
        return $this->view($user, $conversation);
    }

    public function delete(User $user, Conversation $conversation): bool
    {
        return $this->view($user, $conversation);
    }
}
