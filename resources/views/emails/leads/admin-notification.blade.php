<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Lead — {{ $lead->reference }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:24px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;max-width:600px;width:100%;">
                <tr>
                    <td style="background:#e68404;padding:20px 28px;">
                        <h1 style="margin:0;color:#fff;font-size:20px;">New Lead Received</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:28px;">
                        <p style="margin:0 0 20px;font-size:15px;color:#333;">A new inquiry was submitted on <strong>{{ config('app.name') }}</strong>.</p>
                        <table width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #eee;border-radius:6px;font-size:14px;">
                            @foreach([
                                'Reference' => $summary['reference'],
                                'Name' => $summary['name'],
                                'Email' => $summary['email'],
                                'Phone' => $summary['phone'],
                                'Vehicle' => $summary['vehicle'],
                                'Property' => $summary['property'],
                                'Dates' => $summary['dates'],
                                'Pick-up' => $summary['pickup'],
                                'Drop-off' => $summary['dropoff'],
                                'Source' => $summary['source'],
                            ] as $label => $value)
                            <tr style="border-bottom:1px solid #f0f0f0;">
                                <td style="color:#888;width:120px;vertical-align:top;"><strong>{{ $label }}</strong></td>
                                <td style="color:#333;">{{ $value }}</td>
                            </tr>
                            @endforeach
                            @if($summary['message'] !== '—')
                            <tr>
                                <td style="color:#888;vertical-align:top;"><strong>Message</strong></td>
                                <td style="color:#333;">{{ $summary['message'] }}</td>
                            </tr>
                            @endif
                        </table>
                        <p style="margin:28px 0 0;text-align:center;">
                            <a href="{{ $summary['portal_url'] }}" style="display:inline-block;background:#192A30;color:#fff;text-decoration:none;padding:14px 32px;border-radius:6px;font-weight:bold;font-size:15px;">Open Lead in Admin Portal →</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
