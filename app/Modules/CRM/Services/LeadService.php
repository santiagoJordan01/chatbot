<?php

namespace App\Modules\CRM\Services;

use App\Modules\CRM\Events\LeadCreated;
use App\Modules\CRM\Models\Lead;
use App\Modules\CRM\Repositories\Contracts\LeadRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LeadService
{
    public function __construct(private readonly LeadRepositoryInterface $leadRepository)
    {
    }

    public function paginateByBusiness(int $businessId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->leadRepository->paginateByBusiness($businessId, $perPage);
    }

    public function create(array $payload): Lead
    {
        return DB::transaction(function () use ($payload) {
            $lead = $this->leadRepository->create($payload);

            LeadCreated::dispatch($lead);

            return $lead;
        });
    }

    public function update(Lead $lead, array $payload): Lead
    {
        return $this->leadRepository->update($lead, $payload);
    }

    public function delete(Lead $lead): void
    {
        $this->leadRepository->delete($lead);
    }
}
