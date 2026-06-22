<?php

namespace App\Services;

use App\Mail\BookingAdminNotificationMail;
use App\Mail\BookingCustomerConfirmationMail;
use App\Models\Booking;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingEmailService
{
    public function sendConfirmationEmails(Booking $booking, bool $force = false): void
    {
        $cacheKey = 'booking_emails_sent_'.$booking->id;

        if (! $force && Cache::get($cacheKey)) {
            Log::info('Booking emails skipped — already sent', [
                'booking_id' => $booking->id,
                'reference' => $booking->reference,
            ]);

            return;
        }

        if (config('mail.default') === 'log') {
            Log::warning('MAIL_MAILER is "log" — emails are written to laravel.log only, not delivered. Set MAIL_MAILER=smtp on the server.');
        }

        $booking->loadMissing(['vehicle.translations']);
        $sentAny = false;

        try {
            if ($booking->email) {
                Mail::to($booking->email)->send(new BookingCustomerConfirmationMail($booking));
                $sentAny = true;
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
                $sentAny = true;
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

        if ($sentAny) {
            Cache::put($cacheKey, true, now()->addDays(30));
        }
    }
}
