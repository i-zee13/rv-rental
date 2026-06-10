<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;

class PagePublicController extends Controller
{
    public function show($locale = null, $slug = null)
    {
        // Handle route without locale: /pages/{slug} — locale param gets the slug value
        if ($locale && !in_array($locale, ['en', 'es'])) {
            $slug = $locale;
            $locale = null;
        }
        if ($locale) app()->setLocale($locale);

        $page = Page::where('slug', $slug)->firstOrFail();
        $translation = $page->translations->firstWhere('locale', app()->getLocale()) ?? $page->translations->first();

        return view('pages.show', compact('page','translation'));
    }
}
