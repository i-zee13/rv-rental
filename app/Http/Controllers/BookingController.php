<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\Vehicle;
use App\Services\BookingCreatorService;
use App\Services\BookingEmailService;
use App\Services\StripeCheckoutService;
use App\Services\VehicleAvailabilityService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function step1(Request $request)
    {
        $vehicles = Vehicle::where('status', 'available')->with('translations', 'images')->get();

        return view('booking.step1', compact('vehicles'));
    }

    public function postStep1(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => 'required|integer|exists:vehicles,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'pickup_location' => 'nullable|string|max:255',
            'dropoff_location' => 'nullable|string|max:255',
        ]);

        $request->session()->put('booking.step1', $data);

        return redirect()->route('booking.step2');
    }

    public function step2(Request $request)
    {
        $addons = Addon::where('is_active', true)->with('translations')->orderBy('code')->get();
        $step1 = $request->session()->get('booking.step1');
        if (! $step1) {
            return redirect()->route('booking.step1');
        }

        return view('booking.step2', compact('addons', 'step1'));
    }

    public function postStep2(Request $request)
    {
        $data = $request->validate([
            'addon_ids' => 'nullable|array',
            'addon_ids.*' => 'integer|exists:addons,id',
        ]);

        $request->session()->put('booking.step2', $data);

        return redirect()->route('booking.step3');
    }

    public function step3(Request $request)
    {
        $step1 = $request->session()->get('booking.step1');
        if (! $step1) {
            return redirect()->route('booking.step1');
        }
        $step2 = $request->session()->get('booking.step2', []);

        return view('booking.step3', compact('step1', 'step2'));
    }

    public function postStep3(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:2000',
        ]);

        $request->session()->put('booking.step3', $data);

        return redirect()->route('booking.step4');
    }

    public function step4(Request $request, StripeCheckoutService $stripe)
    {
        $step1 = $request->session()->get('booking.step1');
        $step2 = $request->session()->get('booking.step2', []);
        $step3 = $request->session()->get('booking.step3');
        if (! $step1 || ! $step3) {
            return redirect()->route('booking.step1');
        }

        $quote = $this->buildQuote($step1, $step2);
        $stripeEnabled = $stripe->isConfigured();

        return view('booking.step4', compact('step1', 'step2', 'step3', 'quote', 'stripeEnabled'));
    }

    public function createCheckout(Request $request, StripeCheckoutService $stripe, BookingCreatorService $creator)
    {
        $step1 = $request->session()->get('booking.step1');
        $step2 = $request->session()->get('booking.step2', []);
        $step3 = $request->session()->get('booking.step3');

        if (! $step1 || ! $step3) {
            return redirect()->route('booking.step1');
        }

        if (! $stripe->isConfigured()) {
            return back()->with('error', 'Online payment is not available right now. Please reserve and pay at pickup, or contact us.');
        }

        $quote = $this->buildQuote($step1, $step2);
        $vehicle = $quote['vehicle'];

        $availability = app(VehicleAvailabilityService::class);
        if ($availability->isEnabled() && ! $availability->isVehicleBookable($vehicle->id, $quote['start']->toDateString(), $quote['end']->toDateString())) {
            return redirect()->route('booking.step1')->with('error', 'Selected vehicle is not available for those dates.');
        }

        try {
            $booking = $creator->createFromSteps($step1, $step2, $step3, 'pending_payment', sendEmail: false);
            $session = $stripe->createSession($booking, $quote, $step3['email']);

            return redirect()->away($session['url']);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function confirm(Request $request, StripeCheckoutService $stripe, BookingCreatorService $creator)
    {
        if ($sessionId = $request->query('session_id')) {
            return $this->confirmStripeReturn($request, $stripe, $sessionId);
        }

        $step1 = $request->session()->get('booking.step1');
        $step2 = $request->session()->get('booking.step2', []);
        $step3 = $request->session()->get('booking.step3');

        if (! $step1 || ! $step3) {
            return redirect()->route('booking.step1');
        }

        $quote = $this->buildQuote($step1, $step2);
        $vehicle = $quote['vehicle'];

        $availability = app(VehicleAvailabilityService::class);
        if ($availability->isEnabled() && ! $availability->isVehicleBookable($vehicle->id, $quote['start']->toDateString(), $quote['end']->toDateString())) {
            return redirect()->route('booking.step1')->with('error', 'Selected vehicle is not available for those dates.');
        }

        $booking = $creator->createFromSteps($step1, $step2, $step3, 'pending');
        $request->session()->forget('booking');

        return view('booking.confirmation', [
            'booking' => $booking,
            'vehicle' => $vehicle,
            'paid' => false,
        ]);
    }

    protected function confirmStripeReturn(Request $request, StripeCheckoutService $stripe, string $sessionId)
    {
        $result = $stripe->fulfillSession($sessionId);

        if (! $result) {
            return redirect()->route('booking.step4')->with('error', 'Payment could not be verified. If you were charged, contact us with your booking reference.');
        }

        $booking = $result['booking'];
        $vehicle = $booking->vehicle;

        // Always try — skipped automatically if already sent (e.g. user refreshes after 500 error)
        app(BookingEmailService::class)->sendConfirmationEmails($booking);

        $request->session()->forget('booking');

        return view('booking.confirmation', [
            'booking' => $booking,
            'vehicle' => $vehicle,
            'paid' => true,
        ]);
    }

    private function buildQuote(array $step1, array $step2): array
    {
        $creator = app(BookingCreatorService::class);
        $quote = $creator->buildQuote([
            'vehicle_id' => $step1['vehicle_id'],
            'start_date' => $step1['start_date'],
            'end_date' => $step1['end_date'],
            'addon_ids' => $step2['addon_ids'] ?? [],
        ]);

        $vehicle = $quote['vehicle'];
        $translation = $vehicle->translations->firstWhere('locale', app()->getLocale())
            ?? $vehicle->translations->first();

        return array_merge($quote, [
            'vehicle_title' => $translation->title ?? trim($vehicle->make.' '.$vehicle->model),
            'vehicle_image' => $vehicle->images->first()?->publicUrl() ?? '/theme/img/car-2.png',
            'daily_rate' => floatval($vehicle->price_per_day),
        ]);
    }
}
