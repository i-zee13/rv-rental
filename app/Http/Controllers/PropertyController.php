<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function show(Request $request, $id)
    {
        $property = Property::with(['translations', 'images', 'type.translations'])
            ->where('status', '!=', 'hidden')
            ->findOrFail($id);

        $related = Property::with(['translations', 'images'])
            ->where('status', 'available')
            ->where('id', '!=', $property->id)
            ->when($property->property_type_id, fn ($q) => $q->where('property_type_id', $property->property_type_id))
            ->limit(3)
            ->get();

        return view('properties.show', compact('property', 'related'));
    }
}
