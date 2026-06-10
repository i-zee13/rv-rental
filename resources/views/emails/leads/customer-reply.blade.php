<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thank you — {{ config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:24px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;max-width:600px;width:100%;">
                <tr>
                    <td style="background:#192A30;padding:24px 32px;">
                        <h1 style="margin:0;color:#e68404;font-size:22px;">{{ config('app.name') }}</h1>
                        <p style="margin:8px 0 0;color:#ccc;font-size:13px;">Luxury Car Rentals in Miami</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:32px;">
                        <p style="margin:0 0 16px;font-size:16px;color:#333;">{{ $content['greeting'] }}</p>
                        @foreach($content['paragraphs'] as $paragraph)
                            <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#444;">{!! $paragraph !!}</p>
                        @endforeach
                        <p style="margin:24px 0;">
                            <a href="{{ $content['cta_url'] }}" style="display:inline-block;background:#e68404;color:#fff;text-decoration:none;padding:12px 28px;border-radius:50px;font-weight:bold;font-size:14px;">{{ $content['cta_text'] }}</a>
                        </p>
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
