<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteText extends Model
{
    protected $fillable = ['key', 'locale', 'value', 'label', 'group'];

    public static function defaultDefinitions(): array
    {
        $en = lang_path('en/ui.php');
        if (! is_file($en)) {
            return [];
        }

        $lines = require $en;
        $groups = [
            'nav' => ['home', 'vehicles', 'homes_apartments', 'rentals', 'blog', 'about', 'contact', 'get_started', 'language'],
            'footer' => ['quick_links', 'browse_vehicles', 'browse_rentals', 'blog_news', 'about_us', 'terms', 'contact_info', 'whatsapp_us', 'book_vehicle', 'book_vehicle_text', 'start_booking', 'browse_fleet', 'footer_about', 'privacy', 'rights'],
            'hero' => ['hero_slide1_title', 'hero_slide1_sub', 'hero_reserve_title', 'hero_reserve_sub', 'hero_slide2_title', 'hero_slide2_sub', 'hero_quick_search', 'hero_search_placeholder', 'hero_all_categories', 'hero_search_btn', 'hero_slide3_title', 'hero_slide3_sub', 'hero_slide3_extra', 'hero_view_all', 'hero_book_now'],
            'home' => ['home_features_title', 'home_features_sub', 'home_feature1_title', 'home_feature1_text', 'home_feature2_title', 'home_feature2_text', 'home_feature3_title', 'home_feature3_text', 'home_feature4_title', 'home_feature4_text', 'home_about_title', 'home_about_sub', 'home_vision_title', 'home_vision_text', 'home_mission_title', 'home_mission_text', 'home_more_about', 'home_fleet_title', 'home_fleet_sub', 'home_view_all_vehicles', 'home_properties_title', 'home_properties_sub', 'home_process_title', 'home_process_sub', 'home_step1_title', 'home_step1_text', 'home_step2_title', 'home_step2_text', 'home_step3_title', 'home_step3_text'],
        ];

        $definitions = [];
        foreach ($groups as $group => $keys) {
            foreach ($keys as $key) {
                if (array_key_exists($key, $lines)) {
                    $definitions[$key] = [
                        'group' => $group,
                        'label' => str_replace('_', ' ', ucfirst($key)),
                        'default_en' => $lines[$key],
                    ];
                }
            }
        }

        return $definitions;
    }
}
