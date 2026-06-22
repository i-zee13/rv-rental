<?php

namespace App\Services;

use App\Models\Addon;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingCreatorService
{
    public function buildQuote(array $data): array
    {
        $vehicle = Vehicle::with('translations', 'images')->findOrFail($data['vehicle_id']);
        $translation = $vehicle->translations->firstWhere('locale', app()->getLocale())
            ?? $vehicle->translations->first();
        $vehicleTitle = $translation->title ?? trim($vehicle->make . ' ' . $vehicle->model);

        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end = Carbon::parse($data['end_date'])->startOfDay();
        $days = max(1, $start->diffInDays($end) + 1);

        $base = round($days * floatval($vehicle->price_per_day), 2);
        $selectedAddons = collect();
        $addonsTotal = 0;

        if (!empty($data['addon_ids'])) {
            $selectedAddons = Addon::whereIn('id', $data['addon_ids'])->with('translations')->get();
            $addonsTotal = round($selectedAddons->sum('price'), 2);
        }

        $subtotal = round($base + $addonsTotal, 2);
        $taxRate = floatval(config('booking.tax_rate', env('TAX_RATE', 0.10)));
        $taxes = round($subtotal * $taxRate, 2);
        $total = round($subtotal + $taxes, 2);

        return [
            'vehicle' => $vehicle,
            'vehicle_title' => $vehicleTitle,
            'start' => $start,
            'end' => $end,
            'days' => $days,
            'base' => $base,
            'selected_addons' => $selectedAddons,
            'addons_total' => $addonsTotal,
            'subtotal' => $subtotal,
            'taxes' => $taxes,
            'tax_rate' => $taxRate,
            'total' => $total,
        ];
    }

    public function create(array $data, ?string $notes = null, string $status = 'pending', bool $sendEmail = true): Booking
    {
        $booking = DB::transaction(function () use ($data, $notes, $status) {
            $quote = $this->buildQuote($data);
            $vehicle = $quote['vehicle'];
            $start = $quote['start'];
            $end = $quote['end'];

            $customer = Customer::firstOrCreate(
                ['email' => $data['email']],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'phone' => $data['phone'] ?? null,
                ]
            );

            $booking = Booking::create([
                'reference' => 'BK' . strtoupper(uniqid()),
                'vehicle_id' => $vehicle->id,
                'user_id' => Auth::id(),
                'customer_id' => $customer->id,
                'pickup_at' => $start->copy()->setTime(12, 0),
                'return_at' => $end->copy()->setTime(12, 0),
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'pickup_location' => $data['pickup_location'] ?? null,
                'dropoff_location' => $data['dropoff_location'] ?? null,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'notes' => $notes,
                'status' => $status,
                'subtotal' => $quote['subtotal'],
                'taxes' => $quote['taxes'],
                'total' => $quote['total'],
                'currency' => config('booking.currency', env('CURRENCY', 'USD')),
                'extras' => null,
            ]);

            foreach ($quote['selected_addons'] as $addon) {
                $booking->addons()->create([
                    'addon_id' => $addon->id,
                    'price' => $addon->price,
                    'quantity' => 1,
                ]);
            }

            return $booking->fresh(['vehicle', 'addons']);
        });

        if ($sendEmail) {
            app(BookingEmailService::class)->sendConfirmationEmails($booking);
        }

        return $booking;
    }

    public function createFromSteps(array $step1, array $step2, array $step3, string $status = 'pending', bool $sendEmail = true): Booking
    {
        return $this->create([
            'vehicle_id' => $step1['vehicle_id'],
            'start_date' => $step1['start_date'],
            'end_date' => $step1['end_date'],
            'pickup_location' => $step1['pickup_location'] ?? null,
            'dropoff_location' => $step1['dropoff_location'] ?? null,
            'addon_ids' => $step2['addon_ids'] ?? [],
            'first_name' => $step3['first_name'],
            'last_name' => $step3['last_name'],
            'email' => $step3['email'],
            'phone' => $step3['phone'] ?? null,
        ], $step3['notes'] ?? null, $status, $sendEmail);
    }
}
