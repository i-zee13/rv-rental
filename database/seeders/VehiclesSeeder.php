<?php

namespace Database\Seeders;

use App\Support\Slug;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehiclesSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $images = [
            '/theme/img/car-1.png',
            '/theme/img/car-2.png',
            '/theme/img/carousel-1.jpg',
            '/theme/img/carousel-2.jpg',
            '/theme/img/THOR-Vision-Vehicle-TVV-electric-rv-2.jpg',
        ];

        $sample = [
            [
                'category_slug' => 'cars',
                'make' => 'Toyota',
                'model' => 'Camry',
                'year' => '2022',
                'slug' => 'toyota-camry-2022',
                'price' => 59,
                'seats' => 5,
                'featured' => true,
                'title_en' => 'Toyota Camry',
                'title_es' => 'Toyota Camry',
                'desc_en' => 'Reliable midsize sedan — perfect for city driving and business trips in Miami.',
                'desc_es' => 'Sedán mediano confiable — ideal para la ciudad y viajes de negocios en Miami.',
            ],
            [
                'category_slug' => 'rvs',
                'make' => 'Winnebago',
                'model' => 'Adventurer',
                'year' => '2021',
                'slug' => 'winnebago-adventurer-2021',
                'price' => 189,
                'seats' => 6,
                'featured' => true,
                'title_en' => 'Winnebago Adventurer',
                'title_es' => 'Winnebago Aventurero',
                'desc_en' => 'Spacious Class A RV with full kitchen and sleeping for the whole family.',
                'desc_es' => 'RV Clase A espacioso con cocina completa y espacio para toda la familia.',
            ],
            [
                'category_slug' => 'luxury',
                'make' => 'Mercedes-Benz',
                'model' => 'S-Class',
                'year' => '2023',
                'slug' => 'mercedes-s-class-2023',
                'price' => 249,
                'seats' => 5,
                'featured' => true,
                'title_en' => 'Mercedes S-Class',
                'title_es' => 'Mercedes S-Clase',
                'desc_en' => 'Flagship luxury sedan with premium comfort for executive travel.',
                'desc_es' => 'Sedán de lujo insignia con máximo confort para viajes ejecutivos.',
            ],
            [
                'category_slug' => 'suvs',
                'make' => 'Ford',
                'model' => 'Explorer',
                'year' => '2023',
                'slug' => 'ford-explorer-2023',
                'price' => 89,
                'seats' => 7,
                'featured' => false,
                'title_en' => 'Ford Explorer',
                'title_es' => 'Ford Explorer',
                'desc_en' => 'Three-row SUV with room for groups, luggage, and Florida road trips.',
                'desc_es' => 'SUV de tres filas con espacio para grupos, equipaje y viajes por Florida.',
            ],
            [
                'category_slug' => 'luxury',
                'make' => 'BMW',
                'model' => 'X5',
                'year' => '2024',
                'slug' => 'bmw-x5-2024',
                'price' => 159,
                'seats' => 5,
                'featured' => true,
                'title_en' => 'BMW X5',
                'title_es' => 'BMW X5',
                'desc_en' => 'Sport-luxury SUV blending performance with upscale Miami style.',
                'desc_es' => 'SUV deportivo de lujo que combina rendimiento y estilo en Miami.',
            ],
            [
                'category_slug' => 'suvs',
                'make' => 'Chevrolet',
                'model' => 'Suburban',
                'year' => '2022',
                'slug' => 'chevrolet-suburban-2022',
                'price' => 119,
                'seats' => 8,
                'featured' => false,
                'title_en' => 'Chevrolet Suburban',
                'title_es' => 'Chevrolet Suburban',
                'desc_en' => 'Full-size SUV for large families, events, and airport transfers.',
                'desc_es' => 'SUV de tamaño completo para familias grandes, eventos y traslados al aeropuerto.',
            ],
            [
                'category_slug' => 'trucks',
                'make' => 'Ram',
                'model' => '1500',
                'year' => '2023',
                'slug' => 'ram-1500-2023',
                'price' => 99,
                'seats' => 5,
                'featured' => false,
                'title_en' => 'Ram 1500',
                'title_es' => 'Ram 1500',
                'desc_en' => 'Powerful pickup for moving gear, towing, and job-site visits.',
                'desc_es' => 'Pickup potente para mover equipo, remolcar y visitas a obra.',
            ],
            [
                'category_slug' => 'vans',
                'make' => 'Mercedes-Benz',
                'model' => 'Sprinter',
                'year' => '2022',
                'slug' => 'mercedes-sprinter-2022',
                'price' => 139,
                'seats' => 12,
                'featured' => true,
                'title_en' => 'Mercedes Sprinter',
                'title_es' => 'Mercedes Sprinter',
                'desc_en' => 'Passenger van ideal for group outings, tours, and corporate shuttles.',
                'desc_es' => 'Van de pasajeros ideal para grupos, tours y traslados corporativos.',
            ],
            [
                'category_slug' => 'cars',
                'make' => 'Tesla',
                'model' => 'Model 3',
                'year' => '2024',
                'slug' => 'tesla-model-3-2024',
                'price' => 109,
                'seats' => 5,
                'featured' => true,
                'title_en' => 'Tesla Model 3',
                'title_es' => 'Tesla Model 3',
                'desc_en' => 'All-electric sedan — quiet, efficient, and great for eco-conscious travelers.',
                'desc_es' => 'Sedán eléctrico — silencioso, eficiente y perfecto para viajeros eco-conscientes.',
            ],
            [
                'category_slug' => 'luxury',
                'make' => 'Ferrari',
                'model' => '488',
                'year' => '2022',
                'slug' => 'ferrari-488-2022',
                'price' => 499,
                'seats' => 2,
                'featured' => true,
                'title_en' => 'Ferrari 488',
                'title_es' => 'Ferrari 488',
                'desc_en' => 'Exotic supercar for special occasions and unforgettable Miami drives.',
                'desc_es' => 'Superdeportivo exótico para ocasiones especiales y manejar Miami sin olvidar.',
            ],
        ];

        foreach ($sample as $i => $s) {
            $cat = DB::table('vehicle_categories')->where('slug', $s['category_slug'])->first();
            $imagePath = $images[$i % count($images)];

            $existing = DB::table('vehicles')
                ->where('make', $s['make'])
                ->where('model', $s['model'])
                ->where('year', $s['year'])
                ->first();

            $vehicleData = [
                'category_id' => $cat->id ?? null,
                'slug' => $s['slug'],
                'make' => $s['make'],
                'model' => $s['model'],
                'year' => $s['year'],
                'price_per_day' => $s['price'],
                'seats' => $s['seats'],
                'bags' => max(2, (int) floor($s['seats'] / 2)),
                'featured' => $s['featured'],
                'status' => 'available',
                'updated_at' => $now,
            ];

            if ($existing) {
                if (empty($existing->slug)) {
                    $vehicleData['slug'] = Slug::unique(
                        $s['slug'],
                        \App\Models\Vehicle::class,
                        (int) $existing->id
                    );
                } else {
                    unset($vehicleData['slug']);
                }

                DB::table('vehicles')->where('id', $existing->id)->update($vehicleData);
                $vehicleId = $existing->id;
            } else {
                $vehicleId = DB::table('vehicles')->insertGetId(array_merge($vehicleData, [
                    'created_at' => $now,
                ]));
            }

            foreach (['en' => ['title' => $s['title_en'], 'description' => $s['desc_en']], 'es' => ['title' => $s['title_es'], 'description' => $s['desc_es']]] as $locale => $translation) {
                DB::table('vehicle_translations')->updateOrInsert(
                    ['vehicle_id' => $vehicleId, 'locale' => $locale],
                    array_merge($translation, ['specs' => null])
                );
            }

            $hasImage = DB::table('vehicle_images')->where('vehicle_id', $vehicleId)->exists();

            if (! $hasImage) {
                DB::table('vehicle_images')->insert([
                    'vehicle_id' => $vehicleId,
                    'path' => $imagePath,
                    'alt_text' => $s['title_en'].' image',
                    'sort_order' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
