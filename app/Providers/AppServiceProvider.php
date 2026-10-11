<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\DeliveryFeeCalculatorInterface;
use App\Services\Delivery\StandardDeliveryFeeCalculator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        /*
         * Bind the delivery fee abstraction
         * to the standard calculator.
         *
         * Services depend on the interface,
         * while the Service Container decides
         * which implementation should be used.
         */
        $this->app->bind(
            DeliveryFeeCalculatorInterface::class,
            StandardDeliveryFeeCalculator::class
        );
    }

    /**
     * Bootstrap application services.
     */
    public function boot(): void
    {
        /*
         * Detect accidental lazy loading
         * during development and testing.
         */
        Model::preventLazyLoading(
            ! app()->isProduction()
        );
    }
}
