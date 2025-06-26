<?php

namespace App\Providers;

use App\Models\SiteIdentity;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $site = SiteIdentity::first(); // Ambil satu record
            $view->with('site', $site);
        });
    }
}
