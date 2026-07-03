<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
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
        Gate::before(function ($user, string $ability) {
            return $user?->hasRole('super_admin') ? true : null;
        });

        if ($this->app->environment('local') && ! $this->app->runningInConsole()) {
            $request = request();
            URL::forceRootUrl($request->getSchemeAndHttpHost().$request->getBaseUrl());
        }
    }
}
