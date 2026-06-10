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

        $related = Vehicle::with(['translations', 'images'])
            ->where('id', '!=', $vehicle->id)
            ->where('status', 'available')
            ->where('vehicle_category_id', $vehicle->vehicle_category_id)
            ->take(3)
            ->get();

        return view('vehicles.show', compact('vehicle', 'related'));
    }
}
