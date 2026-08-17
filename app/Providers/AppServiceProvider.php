<?php

namespace App\Providers;

use App\Services\MembershipService;
use App\Services\Payments\Contracts\PaymentServiceInterface;
use App\Services\Payments\PaymentManager;
use App\Services\Payments\PaymentVerificationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentManager::class);

        $this->app->singleton(MembershipService::class);

        $this->app->bind(PaymentServiceInterface::class, PaymentVerificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
