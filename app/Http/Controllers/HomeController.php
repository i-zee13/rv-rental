<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;

class HomeController extends Controller
{
    public function index(Request $request, $locale = null)
    {
        if ($locale) app()->setLocale($locale);

        $featured = Vehicle::with(['translations','images'])
            ->where('featured', true)
            ->where('status', 'available')
            ->take(6)->get();

        $categories = \App\Models\VehicleCategory::with('translations')
            ->where('is_active', true)
            ->get();

        $latestPosts = \App\Models\BlogPost::with('translations')
            ->where('status', 'published')
            ->latest()
            ->take(3)
            ->get();

        $totalVehicles = Vehicle::where('status', 'available')->count();

        $allVehicles = Vehicle::with(['translations'])
            ->where('status', 'available')
            ->get();

        $featuredProperties = \App\Models\Property::with(['translations', 'images', 'type.translations'])
            ->where('featured', true)
            ->where('status', 'available')
            ->take(6)->get();

        return view('home', compact('featured', 'categories', 'latestPosts', 'totalVehicles', 'allVehicles', 'featuredProperties'));
    }
}
