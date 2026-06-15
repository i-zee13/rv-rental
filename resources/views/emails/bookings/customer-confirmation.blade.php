@php
    $vehicle = $booking->vehicle;
    $t = $vehicle?->translations?->firstWhere('locale', app()->getLocale()) ?? $vehicle?->translations?->first();
    $vehicleName = $t?->title ?? trim(($vehicle?->make ?? '') . ' ' . ($vehicle?->model ?? ''));
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Confirmed — {{ config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:24px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;max-width:600px;width:100%;">
                <tr>
                    <td style="background:#192A30;padding:24px 32px;">
                        <h1 style="margin:0;color:#e68404;font-size:22px;">{{ config('app.name') }}</h1>
                        <p style="margin:8px 0 0;color:#ccc;font-size:13px;">Booking Confirmation</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:32px;">
                        <p style="margin:0 0 16px;font-size:16px;color:#333;">Hi {{ $booking->first_name }},</p>
                        <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#444;">
                            Thank you for your booking! We have received your reservation and our team will contact you shortly.
                        </p>
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:8px;padding:16px;margin:0 0 20px;">
                            <tr><td style="padding:6px 0;font-size:14px;color:#555;"><strong>Reference:</strong> {{ $booking->reference }}</td></tr>
                            <tr><td style="padding:6px 0;font-size:14px;color:#555;"><strong>Vehicle:</strong> {{ $vehicleName }}</td></tr>
                            <tr><td style="padding:6px 0;font-size:14px;color:#555;"><strong>Dates:</strong> {{ $booking->start_date }} → {{ $booking->end_date }}</td></tr>
                            <tr><td style="padding:6px 0;font-size:14px;color:#555;"><strong>Pickup:</strong> {{ $booking->pickup_location ?? '—' }}</td></tr>
                            <tr><td style="padding:6px 0;font-size:14px;color:#555;"><strong>Return:</strong> {{ $booking->dropoff_location ?? '—' }}</td></tr>
                            <tr><td style="padding:6px 0;font-size:14px;color:#555;"><strong>Total:</strong> {{ $booking->currency }} {{ number_format((float) $booking->total, 2) }}</td></tr>
                        </table>
                        <p style="margin:0 0 16px;font-size:14px;color:#666;">Status: <strong>{{ ucfirst($booking->status) }}</strong></p>
                        <hr style="border:none;border-top:1px solid #eee;margin:24px 0;">
                        <p style="margin:0;font-size:13px;color:#888;">
                            Miami FL 33122 · +1 (786) 978-5809 · info@mvmiamirental.com
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
