<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        if (!$step1) {
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
        if (!$step1) {
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

    public function step4(Request $request)
    {
        $step1 = $request->session()->get('booking.step1');
        $step2 = $request->session()->get('booking.step2', []);
        $step3 = $request->session()->get('booking.step3');
        if (!$step1 || !$step3) {
            return redirect()->route('booking.step1');
        }

        $quote = $this->buildQuote($step1, $step2);

        return view('booking.step4', compact('step1', 'step2', 'step3', 'quote'));
    }

    public function createCheckout(Request $request)
    {
        $step1 = $request->session()->get('booking.step1');
        $step2 = $request->session()->get('booking.step2', []);
        $step3 = $request->session()->get('booking.step3');
        if (!$step1 || !$step3) {
            return redirect()->route('booking.step1');
        }

        $quote = $this->buildQuote($step1, $step2);
        $vehicle = $quote['vehicle'];

        $stripeSecret = env('STRIPE_SECRET');
        if (!$stripeSecret) {
            return back()->with('error', 'Stripe not configured. Set STRIPE_SECRET in .env to enable Checkout.');
        }

        $payload = http_build_query([
            'payment_method_types[]' => 'card',
            'line_items[0][price_data][currency]' => strtolower(env('CURRENCY', 'usd')),
            'line_items[0][price_data][product_data][name]' => $quote['vehicle_title'] . ' rental',
            'line_items[0][price_data][unit_amount]' => intval(round($quote['total'] * 100)),
            'line_items[0][quantity]' => 1,
            'mode' => 'payment',
            'success_url' => url('/booking/confirm?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => url('/booking/step4'),
        ]);

        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_USERPWD, $stripeSecret . ':');
        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return back()->with('error', 'Stripe request failed: ' . $err);
        }

        $json = json_decode($resp, true);
        if (isset($json['url'])) {
            return redirect($json['url']);
        }

        return back()->with('error', 'Stripe error: ' . ($json['error']['message'] ?? 'unknown'));
    }

    public function confirm(Request $request)
    {
        $step1 = $request->session()->get('booking.step1');
        $step2 = $request->session()->get('booking.step2', []);
        $step3 = $request->session()->get('booking.step3');

        if (!$step1 || !$step3) {
            return redirect()->route('booking.step1');
        }

        $quote = $this->buildQuote($step1, $step2);
        $vehicle = $quote['vehicle'];
        $start = $quote['start'];
        $end = $quote['end'];

        $overlap = Booking::where('vehicle_id', $vehicle->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start->toDateString(), $end->toDateString()])
                    ->orWhereBetween('end_date', [$start->toDateString(), $end->toDateString()])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('start_date', '<=', $start->toDateString())
                            ->where('end_date', '>=', $end->toDateString());
                    });
            })->exists();

        if ($overlap) {
            return redirect()->route('booking.step1')->with('error', 'Selected vehicle is not available for those dates.');
        }

        $customer = Customer::firstOrCreate(
            ['email' => $step3['email']],
            [
                'first_name' => $step3['first_name'],
                'last_name' => $step3['last_name'],
                'phone' => $step3['phone'] ?? null,
            ]
        );

        $reference = 'BK' . strtoupper(uniqid());

        $booking = Booking::create([
            'reference' => $reference,
            'vehicle_id' => $vehicle->id,
            'user_id' => Auth::id(),
            'customer_id' => $customer->id,
            'pickup_at' => $start->copy()->setTime(12, 0),
            'return_at' => $end->copy()->setTime(12, 0),
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'pickup_location' => $step1['pickup_location'] ?? null,
            'dropoff_location' => $step1['dropoff_location'] ?? null,
            'first_name' => $step3['first_name'],
            'last_name' => $step3['last_name'],
            'email' => $step3['email'],
            'phone' => $step3['phone'] ?? null,
            'notes' => $step3['notes'] ?? null,
            'status' => 'pending',
            'subtotal' => $quote['subtotal'],
            'taxes' => $quote['taxes'],
            'total' => $quote['total'],
            'currency' => env('CURRENCY', 'USD'),
        ]);

        foreach ($quote['selected_addons'] as $addon) {
            $booking->addons()->create([
                'addon_id' => $addon->id,
                'price' => $addon->price,
                'quantity' => 1,
            ]);
        }

        $request->session()->forget('booking');

        return view('booking.confirmation', compact('booking', 'vehicle'));
    }

    private function buildQuote(array $step1, array $step2): array
    {
        $vehicle = Vehicle::with('translations', 'images')->findOrFail($step1['vehicle_id']);
        $translation = $vehicle->translations->firstWhere('locale', app()->getLocale())
            ?? $vehicle->translations->first();
        $vehicleTitle = $translation->title ?? trim($vehicle->make . ' ' . $vehicle->model);

        $start = Carbon::parse($step1['start_date'])->startOfDay();
        $end = Carbon::parse($step1['end_date'])->startOfDay();
        $days = max(1, $start->diffInDays($end) + 1);

        $base = round($days * floatval($vehicle->price_per_day), 2);
        $selectedAddons = collect();
        $addonsTotal = 0;

        if (!empty($step2['addon_ids'])) {
            $selectedAddons = Addon::whereIn('id', $step2['addon_ids'])->with('translations')->get();
            $addonsTotal = round($selectedAddons->sum('price'), 2);
        }

        $subtotal = round($base + $addonsTotal, 2);
        $taxRate = floatval(env('TAX_RATE', 0.10));
        $taxes = round($subtotal * $taxRate, 2);
        $total = round($subtotal + $taxes, 2);

        return [
            'vehicle' => $vehicle,
            'vehicle_title' => $vehicleTitle,
            'vehicle_image' => $vehicle->images->first()->path ?? '/theme/img/car-2.png',
            'start' => $start,
            'end' => $end,
            'days' => $days,
            'daily_rate' => floatval($vehicle->price_per_day),
            'base' => $base,
            'selected_addons' => $selectedAddons,
            'addons_total' => $addonsTotal,
            'subtotal' => $subtotal,
            'taxes' => $taxes,
            'tax_rate' => $taxRate,
            'total' => $total,
        ];
    }
}
