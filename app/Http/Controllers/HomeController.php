<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;
use App\Models\Vehicle;

class HomeController extends Controller
{
    public function index(Request $request, $locale = null)
    {
        if ($locale) app()->setLocale($locale);

        $featured = Vehicle::with(['translations', 'images'])
            ->where('featured', true)
            ->where('status', 'available')
            ->take(6)->get();

        $carouselVehicles = Vehicle::with(['translations', 'images', 'category.translations'])
            ->where('status', 'available')
            ->orderByDesc('featured')
            ->orderBy('price_per_day')
            ->take(12)
            ->get();

        $categories = \App\Models\VehicleCategory::with('translations')
            ->where('is_active', true)
            ->orderBy('slug')
            ->get();

        $latestPosts = \App\Models\BlogPost::with('translations')
            ->where('status', 'published')
            ->latest()
            ->take(8)
            ->get();

        $totalVehicles = Vehicle::where('status', 'available')->count();

        $allVehicles = Vehicle::with(['translations'])
            ->where('status', 'available')
            ->get();

        $featuredProperties = \App\Models\Property::with(['translations', 'images', 'type.translations'])
            ->where('featured', true)
            ->where('status', 'available')
            ->take(6)->get();

        $carouselProperties = \App\Models\Property::with(['translations', 'images', 'type.translations'])
            ->where('status', 'available')
            ->orderByDesc('featured')
            ->orderBy('price_per_month')
            ->take(12)
            ->get();

        $faqs = Faq::forPage('home');

        return view('home', compact(
            'featured',
            'carouselVehicles',
            'categories',
            'latestPosts',
            'totalVehicles',
            'allVehicles',
            'featuredProperties',
            'carouselProperties',
            'faqs'
        ));
    }
}
