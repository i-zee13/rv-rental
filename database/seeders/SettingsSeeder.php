<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        DB::table('settings')->insert([
            ['key'=>'site_name','value'=>'MV Rental','created_at'=>$now,'updated_at'=>$now],
            ['key'=>'site_description','value'=>'Premium vehicle & RV rental marketplace','created_at'=>$now,'updated_at'=>$now],
            ['key'=>'default_locale','value'=>'en','created_at'=>$now,'updated_at'=>$now],
            ['key'=>'supported_locales','value'=>json_encode(['en','es']),'created_at'=>$now,'updated_at'=>$now],
        ]);
    }
}
