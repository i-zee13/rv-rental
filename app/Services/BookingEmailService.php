<?php

namespace App\Services;

use App\Mail\BookingAdminNotificationMail;
use App\Mail\BookingCustomerConfirmationMail;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingEmailService
{
    public function sendConfirmationEmails(Booking $booking): void
    {
        $booking->loadMissing(['vehicle.translations']);

        try {
            if ($booking->email) {
                Mail::to($booking->email)->send(new BookingCustomerConfirmationMail($booking));
                Log::info('Booking customer email sent', [
                    'booking_id' => $booking->id,
                    'reference' => $booking->reference,
                    'to' => $booking->email,
                ]);
            } else {
                Log::warning('Booking customer email skipped — no email on booking', [
                    'booking_id' => $booking->id,
                    'reference' => $booking->reference,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Booking customer email failed', [
                'booking_id' => $booking->id,
                'reference' => $booking->reference,
                'to' => $booking->email,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            $adminEmail = config('booking.admin_email');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new BookingAdminNotificationMail($booking));
                Log::info('Booking admin email sent', [
                    'booking_id' => $booking->id,
                    'reference' => $booking->reference,
                    'to' => $adminEmail,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Booking admin email failed', [
                'booking_id' => $booking->id,
                'reference' => $booking->reference,
                'to' => config('booking.admin_email'),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
