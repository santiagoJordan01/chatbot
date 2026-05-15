<?php

namespace App\Modules\CRM\Repositories\Contracts;

use App\Modules\CRM\Models\Lead;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LeadRepositoryInterface
{
    public function paginateByBusiness(int $businessId, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Lead;

    public function update(Lead $lead, array $data): Lead;

    public function delete(Lead $lead): void;
}
