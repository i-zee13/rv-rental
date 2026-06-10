<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('vehicle')->orderBy('created_at','desc')->paginate(20);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        $booking = Booking::with(['vehicle','addons'])->findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);
        $booking = Booking::findOrFail($id);
        $booking->status = $request->input('status');
        $booking->save();
        return back()->with('success','Booking status updated.');
    }
}
