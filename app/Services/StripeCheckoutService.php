<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class StripeCheckoutService
{
    public function isConfigured(): bool
    {
        return filled(config('stripe.secret'));
    }

    /**
     * @return array{id: string, url: string}
     */
    public function createSession(Booking $booking, array $quote, string $customerEmail): array
    {
        $secret = config('stripe.secret');
        if (! $secret) {
            throw new RuntimeException('Stripe is not configured.');
        }

        $currency = config('stripe.currency', 'usd');
        $params = [
            'mode' => 'payment',
            'customer_email' => $customerEmail,
            'client_reference_id' => $booking->reference,
            'success_url' => route('booking.confirm').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('booking.step4'),
            'metadata[booking_id]' => (string) $booking->id,
            'metadata[booking_reference]' => $booking->reference,
            'payment_method_types[0]' => 'card',
        ];

        $index = 0;
        $params["line_items[{$index}][quantity]"] = 1;
        $params["line_items[{$index}][price_data][currency]"] = $currency;
        $params["line_items[{$index}][price_data][unit_amount]"] = $this->toCents($quote['base']);
        $params["line_items[{$index}][price_data][product_data][name]"] = $quote['vehicle_title'].' rental ('.$quote['days'].' days)';

        foreach ($quote['selected_addons'] as $addon) {
            $index++;
            $params["line_items[{$index}][quantity]"] = 1;
            $params["line_items[{$index}][price_data][currency]"] = $currency;
            $params["line_items[{$index}][price_data][unit_amount]"] = $this->toCents($addon->price);
            $params["line_items[{$index}][price_data][product_data][name]"] = $addon->name ?? $addon->code;
        }

        if ($quote['taxes'] > 0) {
            $index++;
            $params["line_items[{$index}][quantity]"] = 1;
            $params["line_items[{$index}][price_data][currency]"] = $currency;
            $params["line_items[{$index}][price_data][unit_amount]"] = $this->toCents($quote['taxes']);
            $params["line_items[{$index}][price_data][product_data][name]"] = 'Taxes';
        }

        $response = Http::asForm()
            ->withToken($secret)
            ->post('https://api.stripe.com/v1/checkout/sessions', $params);

        if (! $response->successful()) {
            Log::warning('Stripe checkout session failed', ['body' => $response->body()]);
            throw new RuntimeException($response->json('error.message') ?? 'Could not start Stripe checkout.');
        }

        $session = $response->json();

        Payment::create([
            'booking_id' => $booking->id,
            'provider' => 'stripe',
            'provider_id' => $session['id'],
            'amount' => $quote['total'],
            'currency' => strtoupper($currency),
            'status' => 'pending',
            'meta' => [
                'checkout_url' => $session['url'] ?? null,
            ],
        ]);

        return [
            'id' => $session['id'],
            'url' => $session['url'],
        ];
    }

    public function retrieveSession(string $sessionId): ?array
    {
        $secret = config('stripe.secret');
        if (! $secret) {
            return null;
        }

        $response = Http::withToken($secret)
            ->get('https://api.stripe.com/v1/checkout/sessions/'.$sessionId);

        if (! $response->successful()) {
            Log::warning('Stripe session retrieve failed', ['session_id' => $sessionId, 'body' => $response->body()]);

            return null;
        }

        return $response->json();
    }

    /**
     * Mark booking paid from a completed Stripe Checkout session (idempotent).
     *
     * @return array{booking: Booking, fulfilled: bool}|null
     */
    public function fulfillSession(string $sessionId): ?array
    {
        $existing = Payment::where('provider', 'stripe')
            ->where('provider_id', $sessionId)
            ->where('status', 'paid')
            ->first();

        if ($existing?->booking) {
            return [
                'booking' => $existing->booking->fresh(['vehicle', 'addons']),
                'fulfilled' => false,
            ];
        }

        $session = $this->retrieveSession($sessionId);
        if (! $session || ($session['payment_status'] ?? '') !== 'paid') {
            return null;
        }

        $bookingId = (int) ($session['metadata']['booking_id'] ?? 0);
        $booking = $bookingId ? Booking::find($bookingId) : null;

        if (! $booking) {
            Log::warning('Stripe session missing booking', ['session_id' => $sessionId]);

            return null;
        }

        $wasAlreadyConfirmed = $booking->status === 'confirmed';

        $paidAmount = round(((int) ($session['amount_total'] ?? 0)) / 100, 2);
        if ($paidAmount > 0 && abs($paidAmount - (float) $booking->total) > 0.02) {
            Log::warning('Stripe amount mismatch', [
                'booking_id' => $booking->id,
                'expected' => $booking->total,
                'paid' => $paidAmount,
            ]);
        }

        $payment = Payment::firstOrCreate(
            ['provider' => 'stripe', 'provider_id' => $sessionId],
            [
                'booking_id' => $booking->id,
                'amount' => $paidAmount ?: $booking->total,
                'currency' => strtoupper($session['currency'] ?? config('booking.currency', 'USD')),
                'status' => 'pending',
            ]
        );

        $payment->update([
            'booking_id' => $booking->id,
            'amount' => $paidAmount ?: $booking->total,
            'currency' => strtoupper($session['currency'] ?? $booking->currency),
            'status' => 'paid',
            'meta' => array_merge($payment->meta ?? [], [
                'payment_intent' => $session['payment_intent'] ?? null,
                'customer_email' => $session['customer_details']['email'] ?? null,
            ]),
        ]);

        $booking->update(['status' => 'confirmed']);

        return [
            'booking' => $booking->fresh(['vehicle', 'addons']),
            'fulfilled' => ! $wasAlreadyConfirmed,
        ];
    }

    public function verifyWebhookSignature(string $payload, ?string $signatureHeader): bool
    {
        $secret = config('stripe.webhook_secret');
        if (! $secret || ! $signatureHeader) {
            return false;
        }

        $timestamp = null;
        $signatures = [];

        foreach (explode(',', $signatureHeader) as $part) {
            [$key, $value] = array_map('trim', explode('=', $part, 2) + [null, null]);
            if ($key === 't') {
                $timestamp = $value;
            } elseif ($key === 'v1') {
                $signatures[] = $value;
            }
        }

        if (! $timestamp || $signatures === []) {
            return false;
        }

        if (abs(time() - (int) $timestamp) > 300) {
            return false;
        }

        $signed = $timestamp.'.'.$payload;
        $expected = hash_hmac('sha256', $signed, $secret);

        foreach ($signatures as $sig) {
            if (hash_equals($expected, $sig)) {
                return true;
            }
        }

        return false;
    }

    protected function toCents(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
