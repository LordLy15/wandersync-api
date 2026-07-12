<?php

namespace App\Providers;

use App\Services\BudgetSummaryService;
use App\Services\ShareCodeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BudgetSummaryService::class);
        $this->app->singleton(ShareCodeService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
