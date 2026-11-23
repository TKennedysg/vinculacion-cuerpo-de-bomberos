<?php

namespace App\Providers;

use App\Auth\ActiveUserProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
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

    public function boot(): void
    {
        Auth::provider('active-eloquent', function (Application $app, array $config): ActiveUserProvider {
            return new ActiveUserProvider($app['hash'], $config['model']);
        });
    }
}
