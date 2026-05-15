<?php

namespace App\Modules\CRM\Controllers;

use App\Modules\CRM\Models\Business;
use App\Modules\CRM\Requests\StoreBusinessRequest;
use App\Modules\CRM\Requests\UpdateBusinessRequest;
use App\Modules\CRM\Resources\BusinessResource;
use App\Modules\CRM\Services\BusinessService;
use App\Shared\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessController extends ApiController
{
    public function __construct(private readonly BusinessService $businessService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Business::class);

        $businesses = $this->businessService->paginate((int) $request->integer('per_page', 15));

        return $this->success(BusinessResource::collection($businesses), 'Businesses retrieved');
    }

    public function store(StoreBusinessRequest $request): JsonResponse
    {
        $this->authorize('create', Business::class);

        $business = $this->businessService->create($request->validated());

        return $this->success(new BusinessResource($business), 'Business created', 201);
    }

    public function show(Business $business): JsonResponse
    {
        $this->authorize('view', $business);

        return $this->success(new BusinessResource($business), 'Business retrieved');
    }

    public function update(UpdateBusinessRequest $request, Business $business): JsonResponse
    {
        $this->authorize('update', $business);

        $business = $this->businessService->update($business, $request->validated());

        return $this->success(new BusinessResource($business), 'Business updated');
    }

    public function destroy(Business $business): JsonResponse
    {
        $this->authorize('delete', $business);

        $this->businessService->delete($business);

        return $this->success(null, 'Business deleted');
    }
}
