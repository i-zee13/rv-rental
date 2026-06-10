<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogPost;

class BlogPublicController extends Controller
{
    public function index($locale = null)
    {
        if ($locale && in_array($locale, ['en', 'es'])) app()->setLocale($locale);
        $posts = BlogPost::with('translations')->where('status','published')->paginate(10);
        return view('blog.index', compact('posts'));
    }

    public function show($locale = null, $slug = null)
    {
        // Handle route without locale: /blog/{slug} — locale param gets the slug value
        if ($locale && !in_array($locale, ['en', 'es'])) {
            $slug = $locale;
            $locale = null;
        }
        if ($locale) app()->setLocale($locale);

        $post = BlogPost::where('slug', $slug)->firstOrFail();
        $translation = $post->translations->firstWhere('locale', app()->getLocale()) ?? $post->translations->first();
        return view('blog.show', compact('post','translation'));
    }
}
