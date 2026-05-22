<?php

namespace App\Providers;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
   public function boot()
{

    Paginator::useBootstrapFive();
    // Fix for MySQL < 8
    Schema::defaultStringLength(191);
    DB::statement("SET SESSION collation_connection = 'utf8mb4_unicode_ci'");
}

}
