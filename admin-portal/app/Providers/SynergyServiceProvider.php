<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SynergyWholesale\SynergyWholesale;
use App\Services\SynergyWholesaleService;

class SynergyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SynergyWholesale::class, function () {
            return SynergyWholesale::make(
                config('synergy.reseller_id'),
                config('synergy.api_key'),
            );
        });

        $this->app->singleton(SynergyWholesaleService::class);
    }
}
