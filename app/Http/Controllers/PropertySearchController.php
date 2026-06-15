<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertySearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with(['translations', 'images', 'type.translations'])
            ->where('status', 'available');

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('address_line1', 'like', "%{$keyword}%")
                    ->orWhere('neighborhood', 'like', "%{$keyword}%")
                    ->orWhere('city', 'like', "%{$keyword}%")
                    ->orWhereHas('translations', fn ($t) => $t->where('title', 'like', "%{$keyword}%"));
            });
        }

        if ($request->filled('type')) {
            $query->whereHas('type', fn ($t) => $t->where('slug', $request->type));
        }

        if ($request->filled('beds')) {
            $query->where('bedrooms', '>=', (int) $request->beds);
        }

        if ($request->filled('baths')) {
            $query->where('bathrooms', '>=', (float) $request->baths);
        }

        if ($request->filled('min_price')) {
            $query->where('price_per_month', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_month', '<=', (float) $request->max_price);
        }

        if ($request->boolean('pets')) {
            $query->where('pets_allowed', true);
        }

        if ($request->boolean('furnished')) {
            $query->where('furnished', true);
        }

        if ($request->filled('amenity')) {
            $amenity = $request->amenity;
            $query->whereJsonContains('amenities', $amenity);
        }

        $sort = $request->get('sort', 'featured');
        match ($sort) {
            'price_asc' => $query->orderBy('price_per_month'),
            'price_desc' => $query->orderByDesc('price_per_month'),
            'beds_desc' => $query->orderByDesc('bedrooms'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query->orderByDesc('featured')->orderBy('price_per_month'),
        };

        $properties = $query->paginate(12)->withQueryString();
        $types = PropertyType::with('translations')->where('is_active', true)->orderBy('sort_order')->get();

        return view('properties.search', compact('properties', 'types'));
    }
}
