<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function show(Request $request, $locale = null, $slug = null)
    {
        if ($slug === null && $locale !== null && ! in_array($locale, ['en', 'es'], true)) {
            $slug = $locale;
            $locale = null;
        }

        if ($locale) {
            app()->setLocale($locale);
        }

        // 301 redirect old numeric URLs (/vehicles/1) to slug URLs
        if (is_numeric($slug)) {
            $legacy = Vehicle::find($slug);
            if ($legacy?->slug) {
                return redirect()->route('vehicles.show', $legacy->slug, 301);
            }
        }

        $vehicle = Vehicle::with(['translations', 'images', 'category.translations'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedQuery = Vehicle::with(['translations', 'images'])
            ->where('id', '!=', $vehicle->id)
            ->where('status', 'available');

        if ($vehicle->category_id) {
            $relatedQuery->where('category_id', $vehicle->category_id);
        }

        $related = $relatedQuery->take(3)->get();

        $faqs = Faq::forPage('vehicles.show', [Faq::SCOPE_VEHICLE]);

        return view('vehicles.show', compact('vehicle', 'related', 'faqs'));
    }
}
