<?php

namespace App\Providers;

use App\Services\UciService;
use App\Services\VoucherService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //

        $this->app->bind(UciService::class, function () {
            return new UciService();
        });

        $this->app->bind(VoucherService::class, function () {
            return new VoucherService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
