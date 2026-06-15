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
    <title>New Booking — {{ config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:24px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;max-width:600px;width:100%;">
                <tr>
                    <td style="background:#192A30;padding:24px 32px;">
                        <h1 style="margin:0;color:#e68404;font-size:22px;">New Booking</h1>
                        <p style="margin:8px 0 0;color:#ccc;font-size:13px;">{{ config('app.name') }} Admin</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:32px;">
                        <p style="margin:0 0 16px;font-size:15px;color:#444;">A new booking was submitted via the website.</p>
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:8px;padding:16px;">
                            <tr><td style="padding:6px 0;font-size:14px;"><strong>Reference:</strong> {{ $booking->reference }}</td></tr>
                            <tr><td style="padding:6px 0;font-size:14px;"><strong>Customer:</strong> {{ $booking->first_name }} {{ $booking->last_name }}</td></tr>
                            <tr><td style="padding:6px 0;font-size:14px;"><strong>Email:</strong> {{ $booking->email }}</td></tr>
                            <tr><td style="padding:6px 0;font-size:14px;"><strong>Phone:</strong> {{ $booking->phone ?? '—' }}</td></tr>
                            <tr><td style="padding:6px 0;font-size:14px;"><strong>Vehicle:</strong> {{ $vehicleName }}</td></tr>
                            <tr><td style="padding:6px 0;font-size:14px;"><strong>Dates:</strong> {{ $booking->start_date }} → {{ $booking->end_date }}</td></tr>
                            <tr><td style="padding:6px 0;font-size:14px;"><strong>Pickup:</strong> {{ $booking->pickup_location ?? '—' }}</td></tr>
                            <tr><td style="padding:6px 0;font-size:14px;"><strong>Return:</strong> {{ $booking->dropoff_location ?? '—' }}</td></tr>
                            <tr><td style="padding:6px 0;font-size:14px;"><strong>Total:</strong> {{ $booking->currency }} {{ number_format((float) $booking->total, 2) }}</td></tr>
                            <tr><td style="padding:6px 0;font-size:14px;"><strong>Notes:</strong> {{ $booking->notes ?? '—' }}</td></tr>
                        </table>
                        <p style="margin:24px 0 0;">
                            <a href="{{ url('/admin/bookings/' . $booking->id) }}" style="display:inline-block;background:#e68404;color:#fff;text-decoration:none;padding:12px 28px;border-radius:50px;font-weight:bold;font-size:14px;">View in Admin</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
