<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::with('vehicle')->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        $leads = $query->get();
        $counts = Lead::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.leads.index', compact('leads', 'counts'));
    }

    public function show($id)
    {
        $lead = Lead::with('vehicle.translations')->findOrFail($id);

        return view('admin.leads.show', compact('lead'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Lead::STATUSES)),
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        $lead = Lead::findOrFail($id);
        $lead->status = $request->input('status');
        if ($request->filled('admin_notes')) {
            $lead->admin_notes = $request->input('admin_notes');
        }
        if (in_array($lead->status, ['contacted', 'qualified', 'converted']) && !$lead->contacted_at) {
            $lead->contacted_at = now();
        }
        $lead->save();

        return back()->with('success', 'Lead updated successfully.');
    }
}
