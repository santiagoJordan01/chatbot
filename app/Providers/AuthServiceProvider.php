<?php

namespace App\Providers;

use App\Modules\Automation\Models\Automation;
use App\Modules\Automation\Policies\AutomationPolicy;
use App\Modules\CRM\Models\Appointment;
use App\Modules\CRM\Models\Business;
use App\Modules\CRM\Models\Lead;
use App\Modules\CRM\Policies\AppointmentPolicy;
use App\Modules\CRM\Policies\BusinessPolicy;
use App\Modules\CRM\Policies\LeadPolicy;
use App\Modules\Messaging\Models\Conversation;
use App\Modules\Messaging\Models\Message;
use App\Modules\Messaging\Policies\ConversationPolicy;
use App\Modules\Messaging\Policies\MessagePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Business::class, BusinessPolicy::class);
        Gate::policy(Lead::class, LeadPolicy::class);
        Gate::policy(Conversation::class, ConversationPolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);
        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Gate::policy(Automation::class, AutomationPolicy::class);
    }
}
