<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */

       public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // keep existing code if any
        Schema::defaultStringLength(191);
    }
}
