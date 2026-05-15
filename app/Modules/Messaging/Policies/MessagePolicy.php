<?php

namespace App\Modules\Messaging\Policies;

use App\Models\User;
use App\Modules\Messaging\Models\Message;

class MessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->business_id !== null;
    }

    public function view(User $user, Message $message): bool
    {
        return $user->business_id === $message->business_id;
    }

    public function create(User $user): bool
    {
        return $user->business_id !== null;
    }
}
