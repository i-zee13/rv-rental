<?php

namespace App\Providers;

use App\Services\SeoManager;
use App\Models\SiteText;
use App\Support\ExtensionMimeTypeGuesser;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mime\MimeTypes;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SeoManager::class);

        // Always register — cPanel often enables fileinfo for CLI but not for web PHP.
        MimeTypes::getDefault()->registerGuesser(new ExtensionMimeTypeGuesser());
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        View::composer('layouts.app', function ($view) {
            if (request()->route()) {
                $view->with('seoHead', app(SeoManager::class)->buildForRequest(request()));
            }
        });

        $this->loadSiteTextOverrides();
    }

    protected function loadSiteTextOverrides(): void
    {
        try {
            $grouped = \Illuminate\Support\Facades\Cache::remember('site_texts_by_locale', 3600, function () {
                if (! Schema::hasTable('site_texts')) {
                    return collect();
                }

                return SiteText::query()->get()->groupBy('locale');
            });

            $grouped->each(function ($items, $locale) {
                $lines = $items->pluck('value', 'key')->filter(fn ($v) => $v !== null && $v !== '')->all();
                if ($lines) {
                    Lang::addLines($lines, $locale, 'ui');
                }
            });
        } catch (\Throwable) {
            // DB may be unavailable during deploy/migrate
        }
    }
}
