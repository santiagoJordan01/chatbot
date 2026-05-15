<?php

namespace App\Modules\CRM\Repositories\Eloquent;

use App\Modules\CRM\Models\Business;
use App\Modules\CRM\Repositories\Contracts\BusinessRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BusinessRepository implements BusinessRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Business::query()->latest()->paginate($perPage);
    }

    public function create(array $data): Business
    {
        return Business::query()->create($data);
    }

    public function update(Business $business, array $data): Business
    {
        $business->update($data);

        return $business->refresh();
    }

    public function delete(Business $business): void
    {
        $business->delete();
    }
}
