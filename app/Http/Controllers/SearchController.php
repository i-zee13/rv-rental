<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;

class SearchController extends Controller
{
    public function index(Request $request, $locale = null)
    {
        if ($locale) app()->setLocale($locale);

        $q = trim((string) $request->input('q', ''));
        $category = $request->input('category');

        $query = Vehicle::with(['translations', 'images', 'category.translations'])
            ->where('status', 'available');

        if ($category) {
            $query->whereHas('category', function ($b) use ($category) {
                $b->where('slug', $category)->where('is_active', true);
            });
        }

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function ($sub) use ($like) {
                $sub->where('make', 'like', $like)
                    ->orWhere('model', 'like', $like)
                    ->orWhereHas('translations', function ($t) use ($like) {
                        $t->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }

        $vehicles = $query->paginate(12)->withQueryString();

        $categories = \App\Models\VehicleCategory::with('translations')
            ->where('is_active', true)
            ->orderBy('slug')
            ->get();

        return view('search.results', compact('vehicles', 'categories'));
    }
}
