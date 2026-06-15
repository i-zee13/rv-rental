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
            }
        } catch (\Throwable $e) {
            Log::error('Booking customer email failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            $adminEmail = config('booking.admin_email');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new BookingAdminNotificationMail($booking));
            }
        } catch (\Throwable $e) {
            Log::error('Booking admin email failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
