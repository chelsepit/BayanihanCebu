<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PhysicalDonation;
use App\Observers\PhysicalDonationObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Physical Donation Observer for automatic blockchain recording
        PhysicalDonation::observe(PhysicalDonationObserver::class);
    }
}
