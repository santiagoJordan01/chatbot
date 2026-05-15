<?php

namespace App\Modules\CRM\Controllers;

use App\Modules\CRM\Models\Lead;
use App\Modules\CRM\Requests\StoreLeadRequest;
use App\Modules\CRM\Requests\UpdateLeadRequest;
use App\Modules\CRM\Resources\LeadResource;
use App\Modules\CRM\Services\LeadService;
use App\Shared\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends ApiController
{
    public function __construct(private readonly LeadService $leadService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Lead::class);

        $businessId = (int) $request->integer('business_id', $request->user()?->business_id ?? 0);
        $leads = $this->leadService->paginateByBusiness($businessId, (int) $request->integer('per_page', 15));

        return $this->success(LeadResource::collection($leads), 'Leads retrieved');
    }

    public function store(StoreLeadRequest $request): JsonResponse
    {
        $this->authorize('create', Lead::class);

        $lead = $this->leadService->create($request->validated());

        return $this->success(new LeadResource($lead), 'Lead created', 201);
    }

    public function show(Lead $lead): JsonResponse
    {
        $this->authorize('view', $lead);

        return $this->success(new LeadResource($lead), 'Lead retrieved');
    }

    public function update(UpdateLeadRequest $request, Lead $lead): JsonResponse
    {
        $this->authorize('update', $lead);

        $lead = $this->leadService->update($lead, $request->validated());

        return $this->success(new LeadResource($lead), 'Lead updated');
    }

    public function destroy(Lead $lead): JsonResponse
    {
        $this->authorize('delete', $lead);

        $this->leadService->delete($lead);

        return $this->success(null, 'Lead deleted');
    }
}
