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
            ['slug'=>'cars','en'=>'Cars','es'=>'Coches'],
            ['slug'=>'suvs','en'=>'SUVs','es'=>'SUVs'],
            ['slug'=>'luxury','en'=>'Luxury','es'=>'Lujo'],
            ['slug'=>'vans','en'=>'Vans','es'=>'Furgonetas'],
            ['slug'=>'trucks','en'=>'Trucks','es'=>'Camiones'],
            ['slug'=>'rvs','en'=>'RVs','es'=>'Autocaravanas'],
            ['slug'=>'chauffeur','en'=>'Chauffeur','es'=>'Chofer'],
            ['slug'=>'subscription','en'=>'Subscription','es'=>'Suscripción'],
        ];

        foreach ($items as $item) {
            $catId = DB::table('vehicle_categories')->insertGetId([
                'slug' => $item['slug'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('vehicle_category_translations')->insert([
                ['vehicle_category_id'=>$catId,'locale'=>'en','name'=>$item['en'],'description'=>null],
                ['vehicle_category_id'=>$catId,'locale'=>'es','name'=>$item['es'],'description'=>null],
            ]);
        }
    }
}
