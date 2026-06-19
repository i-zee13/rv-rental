<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
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

        if (is_numeric($slug)) {
            $legacy = Property::find($slug);
            if ($legacy?->slug) {
                return redirect()->route('properties.show', $legacy->slug, 301);
            }
        }

        $property = Property::with(['translations', 'images', 'type.translations'])
            ->where('status', '!=', 'hidden')
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Property::with(['translations', 'images'])
            ->where('status', 'available')
            ->where('id', '!=', $property->id)
            ->when($property->property_type_id, fn ($q) => $q->where('property_type_id', $property->property_type_id))
            ->limit(3)
            ->get();

        $faqs = Faq::forPage('properties.show', [Faq::SCOPE_PROPERTY]);

        return view('properties.show', compact('property', 'related', 'faqs'));
    }
}
