<?php

namespace App\Modules\CRM\Repositories\Contracts;

use App\Modules\CRM\Models\Business;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BusinessRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Business;

    public function update(Business $business, array $data): Business;

    public function delete(Business $business): void;
}
