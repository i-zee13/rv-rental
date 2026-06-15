<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VehiclesSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $sample = [
            ['category_slug' => 'cars', 'make' => 'Toyota', 'model' => 'Camry', 'year' => '2022', 'title_en' => 'Toyota Camry', 'title_es' => 'Toyota Camry'],
            ['category_slug' => 'rvs', 'make' => 'Winnebago', 'model' => 'Adventurer', 'year' => '2021', 'title_en' => 'Winnebago Adventurer', 'title_es' => 'Winnebago Aventurero'],
            ['category_slug' => 'luxury', 'make' => 'Mercedes-Benz', 'model' => 'S-Class', 'year' => '2023', 'title_en' => 'Mercedes S-Class', 'title_es' => 'Mercedes S-Clase'],
        ];

        foreach ($sample as $s) {
            $existing = DB::table('vehicles')
                ->where('make', $s['make'])
                ->where('model', $s['model'])
                ->where('year', $s['year'])
                ->first();

            if ($existing) {
                continue;
            }

            $cat = DB::table('vehicle_categories')->where('slug', $s['category_slug'])->first();
            $vehicleId = DB::table('vehicles')->insertGetId([
                'category_id' => $cat->id ?? null,
                'make' => $s['make'],
                'model' => $s['model'],
                'year' => $s['year'],
                'price_per_day' => rand(50, 300),
                'seats' => 4,
                'bags' => 2,
                'status' => 'available',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('vehicle_translations')->insert([
                ['vehicle_id' => $vehicleId, 'locale' => 'en', 'title' => $s['title_en'], 'description' => 'Sample description in English', 'specs' => null],
                ['vehicle_id' => $vehicleId, 'locale' => 'es', 'title' => $s['title_es'], 'description' => 'Descripción de muestra en Español', 'specs' => null],
            ]);

            DB::table('vehicle_images')->insert([
                ['vehicle_id' => $vehicleId, 'path' => '/media/samples/vehicle_'.$vehicleId.'_1.jpg', 'alt_text' => $s['title_en'].' image', 'sort_order' => 0, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }
    }
}
