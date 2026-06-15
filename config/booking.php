<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Check vehicle availability against existing bookings
    |--------------------------------------------------------------------------
    |
    | Set to false until you configure your availability calendar / booking
    | rules. When false, new bookings are accepted without overlap checks.
    |
    */
    'check_availability' => env('BOOKING_CHECK_AVAILABILITY', false),

    /*
    | Booking statuses that block a vehicle for overlapping dates.
    */
    'blocking_statuses' => ['pending', 'confirmed', 'active', 'completed'],

    'tax_rate' => env('TAX_RATE', 0.10),

    'currency' => env('CURRENCY', 'USD'),

    'admin_email' => env('BOOKING_ADMIN_EMAIL', env('LEADS_ADMIN_EMAIL', env('MAIL_FROM_ADDRESS'))),

];
