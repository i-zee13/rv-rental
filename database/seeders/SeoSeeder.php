<?php

namespace Database\Seeders;

use App\Models\SeoMeta;
use Illuminate\Database\Seeder;

class SeoSeeder extends Seeder
{
    public function run(): void
    {
        $defaultImage = '/theme/img/THOR-Vision-Vehicle-TVV-electric-rv-2.jpg';
        $shareImage = '/theme/img/carousel-1.jpg';

        $defaults = [
            [
                'page_key' => 'global',
                'locale' => 'en',
                'label' => 'Global Defaults',
                'meta_title' => 'MV Miami Rental | Luxury Car Rentals in Miami',
                'meta_description' => 'Luxury and exotic car rentals in Miami. Rent premium vehicles, RVs, and SUVs for your Miami experience. Best prices, fully insured fleet.',
                'meta_keywords' => 'Miami car rental, luxury car rental Miami, exotic cars, rent a car Miami, sports cars, SUV rental',
                'og_title' => 'MV Miami Rental | Luxury Car Rentals in Miami',
                'og_description' => 'Drive luxury in Miami! Rent premium vehicles, RVs, and SUVs with MV Miami Rental.',
                'og_image' => $shareImage,
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
                'twitter_site' => '@mvmiamirental',
                'robots' => 'index,follow',
            ],
            [
                'page_key' => 'home',
                'locale' => 'en',
                'meta_title' => 'Luxury Car Rentals in Miami | MV Miami Rental',
                'meta_description' => 'MV Miami Rental offers the best luxury and exotic car rentals in Miami. Rent premium vehicles for your Miami vacation or business trip.',
                'og_title' => 'Luxury Car Rentals in Miami',
                'og_description' => 'Rent premium vehicles in Miami — luxury cars, RVs, and SUVs. Book online today.',
                'og_image' => $defaultImage,
            ],
            [
                'page_key' => 'search',
                'locale' => 'en',
                'meta_title' => 'Browse Our Fleet — Miami Vehicle Rentals',
                'meta_description' => 'Browse our full fleet of luxury cars, SUVs, and RVs available for rent in Miami. Compare prices and book online.',
                'og_title' => 'Browse Our Miami Rental Fleet',
                'og_description' => 'Explore luxury cars, SUVs, and RVs available for rent in Miami.',
                'og_image' => $shareImage,
            ],
            [
                'page_key' => 'contact',
                'locale' => 'en',
                'meta_title' => 'Contact Us — MV Miami Rental',
                'meta_description' => 'Get in touch with MV Miami Rental. Request a quote, ask about our fleet, or book your luxury vehicle in Miami.',
                'og_title' => 'Contact MV Miami Rental',
                'og_description' => 'Questions about our fleet or booking? Contact our Miami rental team today.',
                'og_image' => $shareImage,
            ],
            [
                'page_key' => 'blog.index',
                'locale' => 'en',
                'meta_title' => 'Blog & News — Miami Rental Tips',
                'meta_description' => 'Read the latest news, travel tips, and Miami driving guides from MV Miami Rental.',
                'og_title' => 'Miami Rental Blog & Travel Tips',
                'og_description' => 'News, travel tips, and Miami driving guides from MV Miami Rental.',
                'og_image' => $shareImage,
            ],
            [
                'page_key' => 'vehicles.show',
                'locale' => 'en',
                'meta_title' => 'Vehicle Rental Miami',
                'meta_description' => 'Rent this premium vehicle in Miami. Instant quotes, flexible pickup, fully insured.',
                'og_title' => 'Rent This Vehicle in Miami',
                'og_description' => 'Premium vehicle rental in Miami — instant quotes, flexible pickup, fully insured.',
                'og_image' => $shareImage,
            ],
            [
                'page_key' => 'booking.step1',
                'locale' => 'en',
                'meta_title' => 'Book a Vehicle — MV Miami Rental',
                'meta_description' => 'Start your Miami vehicle reservation. Select dates, choose add-ons, and confirm your booking.',
                'robots' => 'noindex,follow',
                'noindex' => true,
            ],
            [
                'page_key' => 'leads.thank-you',
                'locale' => 'en',
                'meta_title' => 'Thank You — We Received Your Inquiry',
                'meta_description' => 'Thank you for contacting MV Miami Rental. Our team will respond shortly.',
                'robots' => 'noindex,nofollow',
                'noindex' => true,
            ],
        ];

        foreach ($defaults as $row) {
            SeoMeta::updateOrCreate(
                ['page_key' => $row['page_key'], 'locale' => $row['locale']],
                $row
            );
        }
    }
}
