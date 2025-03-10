<?php

namespace App\Providers;

use App\Repositories\DeviceRepository;
use App\Services\Auth\DeviceService;
use App\Services\Auth\FingerprintService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        // $this->app->singleton(DeviceRepository::class, function () {
        //     return new DeviceRepository();
        // });
        // $this->app->singleton(FingerprintService::class, function () {
        //     return new FingerprintService();
        // });

        // $this->app->singleton(DeviceService::class, function () {
        //     return new DeviceService($this->app->make(DeviceRepository::class), $this->app->make(FingerprintService::class));
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
