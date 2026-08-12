<?php

namespace App\Providers;

use App\Contracts\ActivityLoggerInterface;
use App\Contracts\CustomsServiceInterface;
use App\Contracts\PurchaseOrderServiceInterface;
use App\Contracts\ShipmentServiceInterface;
use App\Contracts\SupplierServiceInterface;
use App\Services\ActivityLogger;
use App\Services\CustomsService;
use App\Services\PurchaseOrderService;
use App\Services\ShipmentService;
use App\Services\SupplierService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Shipment Service
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(
            ShipmentServiceInterface::class,
            ShipmentService::class
        );

        /*
        |--------------------------------------------------------------------------
        | Activity Logger
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(
            ActivityLoggerInterface::class,
            ActivityLogger::class
        );

        /*

        |--------------------------------------------------------------------------
        | Customs Service
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(
            CustomsServiceInterface::class,
            CustomsService::class
        );

        /*

        |--------------------------------------------------------------------------
        | Purchase Order Service
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(
            PurchaseOrderServiceInterface::class,
            PurchaseOrderService::class
        );
        /*

        |--------------------------------------------------------------------------
        | Supplier Service
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(
            SupplierServiceInterface::class,
            SupplierService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Date
        |--------------------------------------------------------------------------
        */

        Date::use(CarbonImmutable::class);

        /*
        |--------------------------------------------------------------------------
        | Database
        |--------------------------------------------------------------------------
        */

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */

        Password::defaults(
            fn (): ?Password => app()->isProduction()
                ? Password::min(12)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
                : null,
        );
    }
}