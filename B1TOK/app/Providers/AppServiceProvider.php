<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Braunson\FatSecret\FatSecret;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрируем FatSecret как синглтон
        $this->app->singleton('fatsecret', function ($app) {
            return new FatSecret(
                config('services.fatsecret.key'),
                config('services.fatsecret.secret')
            );
        });

        // Также регистрируем алиас для фасада
        $this->app->alias('fatsecret', FatSecret::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
