<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VehicleCategoriesSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        $items = [
            ['slug' => 'cars', 'en' => 'Cars', 'es' => 'Coches'],
            ['slug' => 'suvs', 'en' => 'SUVs', 'es' => 'SUVs'],
            ['slug' => 'luxury', 'en' => 'Luxury', 'es' => 'Lujo'],
            ['slug' => 'vans', 'en' => 'Vans', 'es' => 'Furgonetas'],
            ['slug' => 'trucks', 'en' => 'Trucks', 'es' => 'Camiones'],
            ['slug' => 'rvs', 'en' => 'RVs', 'es' => 'Autocaravanas'],
            ['slug' => 'chauffeur', 'en' => 'Chauffeur', 'es' => 'Chofer'],
            ['slug' => 'subscription', 'en' => 'Subscription', 'es' => 'Suscripción'],
        ];

        foreach ($items as $item) {
            $existing = DB::table('vehicle_categories')->where('slug', $item['slug'])->first();

            if ($existing) {
                $catId = $existing->id;
                DB::table('vehicle_categories')->where('id', $catId)->update([
                    'is_active' => true,
                    'updated_at' => $now,
                ]);
            } else {
                $catId = DB::table('vehicle_categories')->insertGetId([
                    'slug' => $item['slug'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach (['en' => $item['en'], 'es' => $item['es']] as $locale => $name) {
                DB::table('vehicle_category_translations')->updateOrInsert(
                    ['vehicle_category_id' => $catId, 'locale' => $locale],
                    ['name' => $name, 'description' => null]
                );
            }
        }
    }
}
