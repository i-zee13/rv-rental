<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Vehicle;

class ContactController extends Controller
{
    public function show()
    {
        $vehicles = Vehicle::with('translations')
            ->where('status', 'available')
            ->get();

        $faqs = Faq::forPage('contact');

        return view('contact', compact('vehicles', 'faqs'));
    }
}
