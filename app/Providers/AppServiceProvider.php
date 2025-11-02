<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\View\Composers\CategoryComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Share categories with all views (skip in testing environment)
        if (!app()->environment('testing')) {
            View::composer('*', CategoryComposer::class);
        }

        // Debug: Log that AppServiceProvider is booting
        \Log::info('AppServiceProvider: Boot method called');
    }
}
