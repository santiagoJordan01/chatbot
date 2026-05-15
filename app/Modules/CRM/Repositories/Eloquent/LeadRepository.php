<?php

namespace App\Modules\CRM\Repositories\Eloquent;

use App\Modules\CRM\Models\Lead;
use App\Modules\CRM\Repositories\Contracts\LeadRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LeadRepository implements LeadRepositoryInterface
{
    public function paginateByBusiness(int $businessId, int $perPage = 15): LengthAwarePaginator
    {
        return Lead::query()
            ->where('business_id', $businessId)
            ->with(['assignedUser'])
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Lead
    {
        return Lead::query()->create($data);
    }

    public function update(Lead $lead, array $data): Lead
    {
        $lead->update($data);

        return $lead->refresh();
    }

    public function delete(Lead $lead): void
    {
        $lead->delete();
    }
}
