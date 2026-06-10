<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Payment;
use App\Models\Lead;

class AdminController extends Controller
{
    public function index()
    {
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status','pending')->count();
        $revenue = Payment::where('status','paid')->sum('amount');
        $availableVehicles = Vehicle::where('status','available')->count();
        $users = User::count();
        $newLeads = Lead::where('status', 'new')->count();
        $totalLeads = Lead::count();

        return view('admin.dashboard', compact('totalBookings','pendingBookings','revenue','availableVehicles','users','newLeads','totalLeads'));
    }
}
