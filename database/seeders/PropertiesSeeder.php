<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertiesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $typeIds = DB::table('property_types')->pluck('id', 'slug');

        $listings = [
            [
                'type' => 'apartment',
                'ref' => 'PR001',
                'address' => '1200 Brickell Bay Dr',
                'neighborhood' => 'Brickell',
                'beds' => 2, 'baths' => 2, 'sqft' => 1050,
                'price' => 3200,
                'amenities' => ['air_conditioning', 'in_unit_laundry', 'pool', 'fitness_center', 'parking'],
                'pets' => true,
                'furnished' => true,
                'featured' => true,
                'en_title' => 'Modern Brickell Apartment with Bay Views',
                'en_desc' => '<p>Spacious 2-bedroom apartment in the heart of Brickell. Floor-to-ceiling windows, in-unit washer/dryer, and resort-style pool. Walk to restaurants, shops, and Metromover.</p>',
                'image' => '/theme/img/carousel-2.jpg',
            ],
            [
                'type' => 'house',
                'ref' => 'PR002',
                'address' => '4520 SW 88th St',
                'neighborhood' => 'Kendall',
                'beds' => 4, 'baths' => 3, 'sqft' => 2400,
                'price' => 4800,
                'amenities' => ['air_conditioning', 'parking', 'yard', 'dishwasher', 'utilities_included'],
                'pets' => true,
                'furnished' => false,
                'featured' => true,
                'en_title' => 'Family Home with Private Yard — Kendall',
                'en_desc' => '<p>Beautiful single-family home with 4 bedrooms, updated kitchen, and fenced backyard. Quiet residential street, close to schools and highways.</p>',
                'image' => '/theme/img/carousel-1.jpg',
            ],
            [
                'type' => 'condo',
                'ref' => 'PR003',
                'address' => '1800 Collins Ave',
                'neighborhood' => 'Miami Beach',
                'beds' => 1, 'baths' => 1, 'sqft' => 780,
                'price' => 2750,
                'amenities' => ['air_conditioning', 'pool', 'balcony', 'elevator', 'gated_community'],
                'pets' => false,
                'furnished' => true,
                'featured' => true,
                'en_title' => 'South Beach Condo Steps from the Ocean',
                'en_desc' => '<p>Stylish 1-bedroom condo on Collins Avenue. Private balcony, building pool, and secure parking. Perfect for seasonal or long-term rental.</p>',
                'image' => '/theme/img/THOR-Vision-Vehicle-TVV-electric-rv-2.jpg',
            ],
            [
                'type' => 'townhouse',
                'ref' => 'PR004',
                'address' => '890 NW 23rd St',
                'neighborhood' => 'Wynwood',
                'beds' => 3, 'baths' => 2.5, 'sqft' => 1650,
                'price' => 3900,
                'amenities' => ['air_conditioning', 'in_unit_laundry', 'parking', 'hardwood_floors', 'balcony'],
                'pets' => true,
                'furnished' => false,
                'featured' => false,
                'en_title' => 'Wynwood Townhouse Near Art District',
                'en_desc' => '<p>Contemporary 3-bed townhouse with rooftop terrace. Minutes from Wynwood Walls, galleries, and dining.</p>',
                'image' => '/theme/img/about-img.jpg',
            ],
            [
                'type' => 'villa',
                'ref' => 'PR005',
                'address' => '10101 Sunset Dr',
                'neighborhood' => 'Coral Gables',
                'beds' => 5, 'baths' => 4, 'sqft' => 3800,
                'price' => 9500,
                'amenities' => ['air_conditioning', 'pool', 'parking', 'yard', 'gated_community', 'fitness_center'],
                'pets' => true,
                'furnished' => true,
                'featured' => true,
                'en_title' => 'Luxury Coral Gables Villa with Pool',
                'en_desc' => '<p>Executive villa with pool, summer kitchen, and 3-car garage. Ideal for corporate housing or extended stays.</p>',
                'image' => '/theme/img/carousel-2.jpg',
            ],
            [
                'type' => 'apartment',
                'ref' => 'PR006',
                'address' => '300 Biscayne Blvd Way',
                'neighborhood' => 'Downtown',
                'beds' => 1, 'baths' => 1, 'sqft' => 650,
                'price' => 2100,
                'amenities' => ['air_conditioning', 'fitness_center', 'elevator', 'pool'],
                'pets' => false,
                'furnished' => false,
                'featured' => false,
                'en_title' => 'Downtown Studio-Style Apartment',
                'en_desc' => '<p>Efficient 1-bed layout in a full-service building. Great for professionals working downtown.</p>',
                'image' => '/theme/img/carousel-1.jpg',
            ],
        ];

        foreach ($listings as $item) {
            $propertyId = DB::table('properties')->insertGetId([
                'property_type_id' => $typeIds[$item['type']] ?? null,
                'reference' => $item['ref'],
                'address_line1' => $item['address'],
                'city' => 'Miami',
                'state' => 'FL',
                'zip' => '33130',
                'neighborhood' => $item['neighborhood'],
                'bedrooms' => $item['beds'],
                'bathrooms' => $item['baths'],
                'sqft' => $item['sqft'],
                'max_guests' => $item['beds'] * 2,
                'min_nights' => 30,
                'price_per_month' => $item['price'],
                'security_deposit' => $item['price'],
                'featured' => $item['featured'],
                'pets_allowed' => $item['pets'],
                'furnished' => $item['furnished'],
                'amenities' => json_encode($item['amenities']),
                'status' => 'available',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('property_translations')->insert([
                [
                    'property_id' => $propertyId,
                    'locale' => 'en',
                    'title' => $item['en_title'],
                    'description' => $item['en_desc'],
                    'highlights' => 'Available for monthly rental. Contact us to schedule a tour.',
                    'meta_title' => $item['en_title'] . ' | MV Miami Rental',
                    'meta_description' => Str::limit(strip_tags($item['en_desc']), 320, ''),
                ],
                [
                    'property_id' => $propertyId,
                    'locale' => 'es',
                    'title' => $item['en_title'],
                    'description' => $item['en_desc'],
                    'highlights' => 'Disponible para alquiler mensual.',
                    'meta_title' => $item['en_title'] . ' | MV Miami Rental',
                    'meta_description' => Str::limit(strip_tags($item['en_desc']), 320, ''),
                ],
            ]);

            DB::table('property_images')->insert([
                'property_id' => $propertyId,
                'path' => $item['image'],
                'alt_text' => $item['en_title'],
                'is_primary' => true,
                'sort_order' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
