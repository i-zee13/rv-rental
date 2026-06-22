<?php

namespace App\Http\Controllers;

use App\Services\BookingEmailService;
use App\Services\StripeCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, StripeCheckoutService $stripe): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        if (! $stripe->verifyWebhookSignature($payload, $signature)) {
            return response('Invalid signature', 400);
        }

        $event = json_decode($payload, true);
        $type = $event['type'] ?? '';

        if ($type === 'checkout.session.completed') {
            $session = $event['data']['object'] ?? [];
            $sessionId = $session['id'] ?? null;

            if ($sessionId) {
                $result = $stripe->fulfillSession($sessionId);
                if ($result && $result['fulfilled']) {
                    try {
                        app(BookingEmailService::class)->sendConfirmationEmails($result['booking']);
                    } catch (\Throwable $e) {
                        Log::warning('Webhook booking email failed', ['message' => $e->getMessage()]);
                    }
                }
            }
        }

        return response('OK', 200);
    }
}
