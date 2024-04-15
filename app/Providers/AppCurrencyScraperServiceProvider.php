<?php

namespace App\Providers;

use App\Services\ScraperService\CurrencyScraperService;
use Illuminate\Support\ServiceProvider;

class AppCurrencyScraperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(CurrencyScraperService::class, function () {
            return new CurrencyScraperService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
