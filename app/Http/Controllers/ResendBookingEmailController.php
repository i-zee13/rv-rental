<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingEmailService;
use Illuminate\Http\Request;

class ResendBookingEmailController extends Controller
{
    public function __invoke(Request $request, BookingEmailService $emails)
    {
        $reference = $request->query('ref');

        if (! $reference) {
            return response()->json([
                'status' => 'error',
                'message' => 'Provide booking reference: ?ref=BKXXXXXXXX',
            ], 422);
        }

        $booking = Booking::where('reference', $reference)->first();

        if (! $booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found for reference: ' . $reference,
            ], 404);
        }

        $emails->sendConfirmationEmails($booking, force: true);

        return response()->json([
            'status' => 'ok',
            'message' => 'Confirmation emails queued for sending (check inbox and laravel.log).',
            'booking' => [
                'reference' => $booking->reference,
                'customer_email' => $booking->email,
                'admin_email' => config('booking.admin_email'),
            ],
        ]);
    }
}
