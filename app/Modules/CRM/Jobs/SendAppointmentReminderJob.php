<?php

namespace App\Modules\CRM\Jobs;

use App\Modules\CRM\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAppointmentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly int $appointmentId)
    {
    }

    public function handle(): void
    {
        $appointment = Appointment::query()->find($this->appointmentId);

        if (! $appointment || $appointment->reminder_sent) {
            return;
        }

        // Placeholder for WhatsApp outbound integration.
        $appointment->forceFill(['reminder_sent' => true])->save();
    }
}
