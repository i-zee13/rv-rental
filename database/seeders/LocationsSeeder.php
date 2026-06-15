<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LocationsSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $cities = [
            ['city' => 'Los Angeles', 'state' => 'CA', 'country' => 'US', 'title_en' => 'Los Angeles, CA', 'title_es' => 'Los Ángeles, CA'],
            ['city' => 'New York', 'state' => 'NY', 'country' => 'US', 'title_en' => 'New York, NY', 'title_es' => 'Nueva York, NY'],
            ['city' => 'Miami', 'state' => 'FL', 'country' => 'US', 'title_en' => 'Miami, FL', 'title_es' => 'Miami, FL'],
        ];

        foreach ($cities as $c) {
            $existing = DB::table('locations')
                ->where('type', 'city')
                ->where('city', $c['city'])
                ->where('state', $c['state'])
                ->where('country', $c['country'])
                ->first();

            if ($existing) {
                $locId = $existing->id;
                DB::table('locations')->where('id', $locId)->update([
                    'is_active' => true,
                    'updated_at' => $now,
                ]);
            } else {
                $locId = DB::table('locations')->insertGetId([
                    'type' => 'city',
                    'address' => null,
                    'city' => $c['city'],
                    'state' => $c['state'],
                    'country' => $c['country'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach (['en' => $c['title_en'], 'es' => $c['title_es']] as $locale => $title) {
                DB::table('location_translations')->updateOrInsert(
                    ['location_id' => $locId, 'locale' => $locale],
                    ['title' => $title, 'description' => null]
                );
            }
        }
    }
}
