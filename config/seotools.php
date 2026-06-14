<?php
/**
 * @see https://github.com/artesaos/seotools
 */

return [
    'inertia' => env('SEO_TOOLS_INERTIA', false),
    'meta' => [
        /*
         * The default configurations to be used by the meta generator.
         */
        'defaults'       => [
            'title'        => env('APP_NAME', 'MV Miami Rental'),
            'titleBefore'  => false,
            'description'  => 'Luxury and exotic car rentals in Miami. Rent premium vehicles, RVs, and SUVs — fully insured fleet, best prices.',
            'separator'    => ' | ',
            'keywords'     => ['Miami car rental', 'luxury car rental Miami', 'exotic cars', 'rent a car Miami'],
            'canonical'    => 'current',
            'robots'       => 'index,follow',
        ],
        /*
         * Webmaster tags are always added.
         */
        'webmaster_tags' => [
            'google'    => null,
            'bing'      => null,
            'alexa'     => null,
            'pinterest' => null,
            'yandex'    => null,
            'norton'    => null,
        ],

        'add_notranslate_class' => false,
    ],
    'opengraph' => [
        /*
         * The default configurations to be used by the opengraph generator.
         */
        'defaults' => [
            'title'       => env('APP_NAME', 'MV Miami Rental'),
            'description' => 'Luxury and exotic car rentals in Miami. Rent premium vehicles, RVs, and SUVs — fully insured fleet, best prices.',
            'url'         => null,
            'type'        => 'website',
            'site_name'   => env('APP_NAME', 'MV Miami Rental'),
            'images'      => [],
        ],
    ],

    /*
    | Default share image when no page-specific OG image is set (must be a public URL path).
    */
    'default_og_image' => env('SEO_DEFAULT_OG_IMAGE', '/theme/img/THOR-Vision-Vehicle-TVV-electric-rv-2.jpg'),
    'twitter' => [
        /*
         * The default values to be used by the twitter cards generator.
         */
        'defaults' => [
            //'card'        => 'summary',
            //'site'        => '@LuizVinicius73',
        ],
    ],
    'json-ld' => [
        /*
         * The default configurations to be used by the json-ld generator.
         */
        'defaults' => [
            'title'       => env('APP_NAME', 'MV Miami Rental'),
            'description' => 'Luxury and exotic car rentals in Miami. Rent premium vehicles, RVs, and SUVs — fully insured fleet, best prices.',
            'url'         => 'current',
            'type'        => 'WebPage',
            'images'      => [],
        ],
    ],
];
