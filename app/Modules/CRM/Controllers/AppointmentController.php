<?php

namespace App\Modules\CRM\Controllers;

use App\Modules\CRM\Models\Appointment;
use App\Modules\CRM\Requests\RescheduleAppointmentRequest;
use App\Modules\CRM\Requests\StoreAppointmentRequest;
use App\Modules\CRM\Requests\UpdateAppointmentRequest;
use App\Modules\CRM\Resources\AppointmentResource;
use App\Shared\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Appointment::class);

        $businessId = (int) $request->integer('business_id', $request->user()?->business_id ?? 0);

        $appointments = Appointment::query()
            ->with('lead')
            ->where('business_id', $businessId)
            ->latest('starts_at')
            ->paginate((int) $request->integer('per_page', 15));

        return $this->success(AppointmentResource::collection($appointments), 'Appointments retrieved');
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $this->authorize('create', Appointment::class);

        $appointment = Appointment::query()->create([
            ...$request->validated(),
            'created_by' => $request->user()?->id,
        ]);

        return $this->success(new AppointmentResource($appointment), 'Appointment created', 201);
    }

    public function show(Appointment $appointment): JsonResponse
    {
        $this->authorize('view', $appointment);

        return $this->success(new AppointmentResource($appointment), 'Appointment retrieved');
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        $this->authorize('update', $appointment);

        $appointment->update($request->validated());

        return $this->success(new AppointmentResource($appointment->refresh()), 'Appointment updated');
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        $this->authorize('delete', $appointment);

        $appointment->delete();

        return $this->success(null, 'Appointment deleted');
    }

    public function confirm(Appointment $appointment): JsonResponse
    {
        $this->authorize('update', $appointment);

        $appointment->update(['status' => 'confirmed']);
        $appointment->refresh()->load('lead');

        app(\App\Modules\Automation\Services\AutomationRunner::class)->run(
            $appointment->business_id,
            'appointment.confirmed',
            ['lead_id' => $appointment->lead_id],
        );

        return $this->success(new AppointmentResource($appointment), 'Appointment confirmed');
    }

    public function cancel(Appointment $appointment, Request $request): JsonResponse
    {
        $this->authorize('update', $appointment);

        $validated = $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $appointment->update([
            'status' => 'canceled',
            'notes' => $validated['notes'] ?? $appointment->notes,
        ]);

        return $this->success(new AppointmentResource($appointment->refresh()), 'Appointment canceled');
    }

    public function reschedule(RescheduleAppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        $this->authorize('update', $appointment);

        $appointment->update([
            'status' => 'rescheduled',
            'starts_at' => $request->validated('starts_at'),
            'ends_at' => $request->validated('ends_at'),
            'notes' => $request->validated('notes') ?? $appointment->notes,
            'reminder_sent' => false,
        ]);

        return $this->success(new AppointmentResource($appointment->refresh()), 'Appointment rescheduled');
    }
}
