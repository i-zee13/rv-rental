<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function show($locale = null, $id = null)
    {
        if (is_numeric($locale)) {
            $id = $locale;
            $locale = null;
        }
        if ($locale) app()->setLocale($locale);

        $vehicle = Vehicle::with(['translations', 'images', 'category.translations'])->findOrFail($id);

        $relatedQuery = Vehicle::with(['translations', 'images'])
            ->where('id', '!=', $vehicle->id)
            ->where('status', 'available');

        if ($vehicle->category_id) {
            $relatedQuery->where('category_id', $vehicle->category_id);
        }

        $related = $relatedQuery->take(3)->get();

        return view('vehicles.show', compact('vehicle', 'related'));
    }
}
