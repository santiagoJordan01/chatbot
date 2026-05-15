<?php

namespace App\Modules\Automation\Controllers;

use App\Modules\Automation\Models\Automation;
use App\Modules\Automation\Requests\StoreAutomationRequest;
use App\Modules\Automation\Requests\UpdateAutomationRequest;
use App\Modules\Automation\Resources\AutomationResource;
use App\Shared\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutomationController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Automation::class);

        $businessId = (int) $request->integer('business_id', $request->user()?->business_id ?? 0);

        $automations = Automation::query()
            ->where('business_id', $businessId)
            ->latest()
            ->paginate((int) $request->integer('per_page', 15));

        return $this->success(AutomationResource::collection($automations), 'Automations retrieved');
    }

    public function store(StoreAutomationRequest $request): JsonResponse
    {
        $this->authorize('create', Automation::class);

        $automation = Automation::query()->create($request->validated());

        return $this->success(new AutomationResource($automation), 'Automation created', 201);
    }

    public function show(Automation $automation): JsonResponse
    {
        $this->authorize('view', $automation);

        return $this->success(new AutomationResource($automation), 'Automation retrieved');
    }

    public function update(UpdateAutomationRequest $request, Automation $automation): JsonResponse
    {
        $this->authorize('update', $automation);

        $automation->update($request->validated());

        return $this->success(new AutomationResource($automation->refresh()), 'Automation updated');
    }

    public function destroy(Automation $automation): JsonResponse
    {
        $this->authorize('delete', $automation);

        $automation->delete();

        return $this->success(null, 'Automation deleted');
    }
}
