<?php

namespace App\Providers;

use App\Support\Breadcrumbs;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
    public function boot(): void
    {
        // Set PHP's default timezone to Asia/Manila
        date_default_timezone_set(Config::get('app.timezone'));

        // Optional: Set Carbon locale if you use translated dates
        Carbon::setLocale(Config::get('app.locale'));

        View::composer(['layouts.app', 'layouts.public'], function ($view) {
            $override = $view->getData()['breadcrumbs'] ?? null;

            $view->with(
                'breadcrumbItems',
                Breadcrumbs::resolve(Route::currentRouteName(), is_array($override) ? $override : null),
            );
        });
    }
}
