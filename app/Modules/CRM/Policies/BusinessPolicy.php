<?php

namespace App\Modules\CRM\Policies;

use App\Models\User;
use App\Modules\CRM\Models\Business;

class BusinessPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, Business $business): bool
    {
        return $user->business_id === null || $user->business_id === $business->id;
    }

    public function create(User $user): bool
    {
        return $user->exists;
    }

    public function update(User $user, Business $business): bool
    {
        return $this->view($user, $business);
    }

    public function delete(User $user, Business $business): bool
    {
        return $this->view($user, $business);
    }
}
