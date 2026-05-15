<?php

namespace App\Modules\CRM\Services;

use App\Modules\CRM\Models\Business;
use App\Modules\CRM\Repositories\Contracts\BusinessRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BusinessService
{
    public function __construct(private readonly BusinessRepositoryInterface $businessRepository)
    {
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->businessRepository->paginate($perPage);
    }

    public function create(array $payload): Business
    {
        return $this->businessRepository->create($payload);
    }

    public function update(Business $business, array $payload): Business
    {
        return $this->businessRepository->update($business, $payload);
    }

    public function delete(Business $business): void
    {
        $this->businessRepository->delete($business);
    }
}
