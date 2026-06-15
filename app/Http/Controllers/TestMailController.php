<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TestMailController extends Controller
{
    public function __invoke(Request $request)
    {
        $to = $request->query('to', config('booking.admin_email') ?: config('mail.from.address'));

        if (! $to || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Provide a valid ?to=email@example.com query parameter.',
            ], 422);
        }

        $smtp = config('mail.mailers.smtp', []);

        $diagnostics = [
            'mailer' => config('mail.default'),
            'host' => $smtp['host'] ?? null,
            'port' => $smtp['port'] ?? null,
            'encryption' => $smtp['encryption'] ?? null,
            'scheme' => $smtp['scheme'] ?? null,
            'username' => $smtp['username'] ?? null,
            'password_set' => ! empty($smtp['password']),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'booking_admin_email' => config('booking.admin_email'),
            'app_env' => config('app.env'),
            'config_cached' => app()->configurationIsCached(),
            'openssl_loaded' => extension_loaded('openssl'),
            'fileinfo_loaded' => extension_loaded('fileinfo'),
        ];

        try {
            Mail::raw(
                'This is a test email from ' . config('app.name') . ' at ' . now()->toDateTimeString() . '.',
                function ($message) use ($to) {
                    $message->to($to)->subject('Test Email — ' . config('app.name'));
                }
            );

            Log::info('Test email sent', ['to' => $to]);

            return response()->json([
                'status' => 'ok',
                'message' => 'Test email sent successfully.',
                'sent_to' => $to,
                'diagnostics' => $diagnostics,
            ]);
        } catch (\Throwable $e) {
            Log::error('Test email failed', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'sent_to' => $to,
                'diagnostics' => $diagnostics,
                'hints' => [
                    'Confirm MAIL_* values exist in the server .env (not only on your PC).',
                    'After changing .env run /clear?no_cache=1 then test again.',
                    'Gmail needs an App Password (2FA on): https://myaccount.google.com/apppasswords',
                    'Some hosts block port 587 — try MAIL_PORT=465 and MAIL_ENCRYPTION=ssl',
                    'Enable php_fileinfo in cPanel → Select PHP Version → Extensions (recommended).',
                    'Check storage/logs/laravel.log for "Booking customer email failed".',
                ],
            ], 500);
        }
    }
}
