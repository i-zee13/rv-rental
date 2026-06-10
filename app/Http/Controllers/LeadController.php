<?php

namespace App\Http\Controllers;

use App\Mail\LeadThankYouMail;
use App\Mail\NewLeadAdminMail;
use App\Models\Lead;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        // Honeypot — bots fill hidden field
        if ($request->filled('website_url')) {
            return redirect()->route('leads.thank-you');
        }

        $key = 'lead-submit:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, config('leads.rate_limit', 5))) {
            return back()->withInput()->with('error', 'Too many submissions. Please try again later.');
        }
        RateLimiter::hit($key, 3600);

        $validated = $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'nullable|string|max:100',
            'email'            => 'required|email|max:191',
            'phone'            => 'nullable|string|max:30',
            'vehicle_id'       => 'nullable|integer|exists:vehicles,id',
            'pickup_location'  => 'nullable|string|max:255',
            'dropoff_location' => 'nullable|string|max:255',
            'pickup_date'      => 'nullable|date|after_or_equal:today',
            'dropoff_date'     => 'nullable|date|after_or_equal:pickup_date',
            'pickup_time'      => 'nullable|string|max:20',
            'dropoff_time'     => 'nullable|string|max:20',
            'message'          => 'nullable|string|max:5000',
            'source'           => 'nullable|string|max:50',
        ]);

        $vehicleName = null;
        if (!empty($validated['vehicle_id'])) {
            $vehicle = Vehicle::with('translations')->find($validated['vehicle_id']);
            if ($vehicle) {
                $t = $vehicle->translations->firstWhere('locale', app()->getLocale())
                    ?? $vehicle->translations->first();
                $vehicleName = $t->title ?? ($vehicle->make . ' ' . $vehicle->model);
            }
        }

        $lead = Lead::create([
            'reference'        => Lead::generateReference(),
            'status'           => 'new',
            'source'           => $validated['source'] ?? 'website',
            'vehicle_id'       => $validated['vehicle_id'] ?? null,
            'vehicle_name'     => $vehicleName,
            'first_name'       => $validated['first_name'],
            'last_name'        => $validated['last_name'] ?? null,
            'email'            => $validated['email'],
            'phone'            => $validated['phone'] ?? null,
            'pickup_location'  => $validated['pickup_location'] ?? null,
            'dropoff_location' => $validated['dropoff_location'] ?? null,
            'pickup_date'      => $validated['pickup_date'] ?? null,
            'dropoff_date'     => $validated['dropoff_date'] ?? null,
            'pickup_time'      => $validated['pickup_time'] ?? null,
            'dropoff_time'     => $validated['dropoff_time'] ?? null,
            'message'          => $validated['message'] ?? null,
            'ip_address'       => $request->ip(),
            'user_agent'       => substr((string) $request->userAgent(), 0, 500),
            'locale'           => app()->getLocale(),
        ]);

        $this->sendLeadEmails($lead);

        return redirect()->route('leads.thank-you', ['ref' => $lead->reference])
            ->with('success', 'Thank you! We received your inquiry.');
    }

    public function thankYou(Request $request)
    {
        return view('leads.thank-you', [
            'reference' => $request->query('ref'),
        ]);
    }

    protected function sendLeadEmails(Lead $lead): void
    {
        try {
            Mail::to($lead->email)->send(new LeadThankYouMail($lead));
            $lead->update(['customer_email_sent' => true]);
        } catch (\Throwable $e) {
            Log::error('Lead customer email failed', ['lead_id' => $lead->id, 'error' => $e->getMessage()]);
        }

        try {
            $adminEmail = config('leads.admin_email');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new NewLeadAdminMail($lead));
                $lead->update(['admin_email_sent' => true]);
            }
        } catch (\Throwable $e) {
            Log::error('Lead admin email failed', ['lead_id' => $lead->id, 'error' => $e->getMessage()]);
        }
    }
}
