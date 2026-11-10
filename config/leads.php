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

    /*
    |--------------------------------------------------------------------------
    | Message spam scoring
    |--------------------------------------------------------------------------
    | Score >= threshold → save as spam, skip notification emails.
    */
    'spam_score_threshold' => 3,

    'spam_url_score' => 3,

    'spam_phrases' => [
        // Sales / outreach
        'for your business',
        'useful for your business',
        'free tool',
        'free trial',
        '14-day free trial',
        'special offer',
        'one time fee',
        'no strings attached',
        'without paying for ads',
        'manual outreach',
        'automated outreach',
        'outreach system',
        'unsubscribe',

        // Lead gen / marketing
        'lead generation',
        'lead response',
        'web lead',
        'ai voice agent',
        'b2b decision',
        'business website database',
        'email marketing',
        'email offers',
        'guest post',
        'backlink',

        // SEO / directories
        'directories',
        'submit your site',
        'list your site',
        'competitor website',
        'ranking for',
        'hundreds of keywords',

        // Social growth pitches
        'instagram growth',
        'followers per month',
        'growth service',

        // Generic cold-pitch openers tied to site
        'checking out mvmiamirental',
        'regarding mvmiamirental',
        'mvmiamirental.com team',
        'mvmiamirental com team',
    ],

    'spam_phrase_score' => 2,
];
