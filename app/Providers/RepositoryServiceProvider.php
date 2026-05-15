<?php

namespace App\Providers;

use App\Modules\CRM\Repositories\Contracts\BusinessRepositoryInterface;
use App\Modules\CRM\Repositories\Contracts\LeadRepositoryInterface;
use App\Modules\CRM\Repositories\Eloquent\BusinessRepository;
use App\Modules\CRM\Repositories\Eloquent\LeadRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BusinessRepositoryInterface::class, BusinessRepository::class);
        $this->app->bind(LeadRepositoryInterface::class, LeadRepository::class);
    }
}
