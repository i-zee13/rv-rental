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
            ['city'=>'Los Angeles','state'=>'CA','country'=>'US','slug'=>'los-angeles','title_en'=>'Los Angeles, CA','title_es'=>'Los Ángeles, CA'],
            ['city'=>'New York','state'=>'NY','country'=>'US','slug'=>'new-york','title_en'=>'New York, NY','title_es'=>'Nueva York, NY'],
            ['city'=>'Miami','state'=>'FL','country'=>'US','slug'=>'miami','title_en'=>'Miami, FL','title_es'=>'Miami, FL'],
        ];

        foreach ($cities as $c) {
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

            DB::table('location_translations')->insert([
                ['location_id'=>$locId,'locale'=>'en','title'=>$c['title_en'],'description'=>null],
                ['location_id'=>$locId,'locale'=>'es','title'=>$c['title_es'],'description'=>null],
            ]);
        }
    }
}
