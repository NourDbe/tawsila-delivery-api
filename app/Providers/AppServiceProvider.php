<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

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
 * Bootstrap application services.
 */
public function boot(): void
{
    /*
     * Detect accidental lazy loading
     * during development and testing.
     *
     * This helps us catch N+1 problems early.
     */
    Model::preventLazyLoading(
        ! app()->isProduction()
    );
}
}
