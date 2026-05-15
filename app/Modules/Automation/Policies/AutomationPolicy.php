<?php

namespace App\Modules\Automation\Policies;

use App\Models\User;
use App\Modules\Automation\Models\Automation;

class AutomationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->business_id !== null;
    }

    public function view(User $user, Automation $automation): bool
    {
        return $user->business_id === $automation->business_id;
    }

    public function create(User $user): bool
    {
        return $user->business_id !== null;
    }

    public function update(User $user, Automation $automation): bool
    {
        return $this->view($user, $automation);
    }

    public function delete(User $user, Automation $automation): bool
    {
        return $this->view($user, $automation);
    }
}
