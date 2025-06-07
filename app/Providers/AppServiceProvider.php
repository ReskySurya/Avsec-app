<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Views\Components\FormHHMD;
use App\Views\Components\FormWTMD;
use App\Views\Components\FormXRAY;

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
        // Register custom components
        Blade::component('form-hhmd', FormHHMD::class);
        Blade::component('form-wtmd', FormWTMD::class);
        Blade::component('form-xray', FormXRAY::class);
    }
}
