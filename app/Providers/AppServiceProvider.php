<?php

namespace App\Providers;

use App\Services\SeoManager;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SeoManager::class);
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        View::composer('layouts.app', function ($view) {
            if (request()->route()) {
                app(SeoManager::class)->applyForRequest(request());
            }
        });
    }
}
