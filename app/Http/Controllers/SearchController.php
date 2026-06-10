<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;

class SearchController extends Controller
{
    public function index(Request $request, $locale = null)
    {
        if ($locale) app()->setLocale($locale);

        $q = $request->input('q');
        $category = $request->input('category');

        $query = Vehicle::with(['translations','images']);

        if ($category) {
            $query->whereHas('category', function($b) use ($category) {
                $b->where('slug', $category);
            });
        }

        if ($q) {
            $query->whereHas('translations', function($t) use ($q) {
                $t->where('title', 'like', "%$q%");
            });
        }

        $vehicles = $query->paginate(12)->withQueryString();

        return view('search.results', compact('vehicles'));
    }
}
