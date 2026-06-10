<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;

class ContactController extends Controller
{
    public function show()
    {
        $vehicles = Vehicle::with('translations')
            ->where('status', 'available')
            ->get();

        return view('contact', compact('vehicles'));
    }
}
