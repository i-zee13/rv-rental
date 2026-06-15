<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;

class PagePublicController extends Controller
{
    public function about($locale = null)
    {
        if ($locale && in_array($locale, ['en', 'es'], true)) {
            app()->setLocale($locale);
        }

        return $this->renderPage(['about-us', 'about']);
    }

    public function show($locale = null, $slug = null)
    {
        // Handle route without locale: /pages/{slug} — locale param gets the slug value
        if ($locale && !in_array($locale, ['en', 'es'])) {
            $slug = $locale;
            $locale = null;
        }
        if ($locale) {
            app()->setLocale($locale);
        }

        if ($slug === 'about') {
            return redirect()->route('about', [], 301);
        }

        return $this->renderPage([$slug]);
    }

    protected function renderPage(array $slugs)
    {
        $page = Page::with('translations')
            ->whereIn('slug', $slugs)
            ->where('is_published', true)
            ->firstOrFail();

        $translation = $page->translations->firstWhere('locale', app()->getLocale())
            ?? $page->translations->first();

        return view('pages.show', compact('page', 'translation'));
    }
}
