<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin notification email(s) for new leads
    |--------------------------------------------------------------------------
    */
    'admin_email' => env('LEADS_ADMIN_EMAIL', env('MAIL_FROM_ADDRESS', 'admin@example.com')),

    /*
    |--------------------------------------------------------------------------
    | Rate limit: max submissions per IP per hour
    |--------------------------------------------------------------------------
    */
    'rate_limit' => (int) env('LEADS_RATE_LIMIT', 5),
];
