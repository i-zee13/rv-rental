<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyTypesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $types = [
            ['slug' => 'apartment', 'sort' => 1, 'en' => 'Apartment', 'es' => 'Apartamento'],
            ['slug' => 'house', 'sort' => 2, 'en' => 'House', 'es' => 'Casa'],
            ['slug' => 'condo', 'sort' => 3, 'en' => 'Condo', 'es' => 'Condominio'],
            ['slug' => 'townhouse', 'sort' => 4, 'en' => 'Townhouse', 'es' => 'Townhouse'],
            ['slug' => 'villa', 'sort' => 5, 'en' => 'Villa', 'es' => 'Villa'],
        ];

        foreach ($types as $type) {
            $id = DB::table('property_types')->insertGetId([
                'slug' => $type['slug'],
                'is_active' => true,
                'sort_order' => $type['sort'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('property_type_translations')->insert([
                ['property_type_id' => $id, 'locale' => 'en', 'name' => $type['en'], 'description' => null],
                ['property_type_id' => $id, 'locale' => 'es', 'name' => $type['es'], 'description' => null],
            ]);
        }
    }
}
