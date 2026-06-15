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
        $settings = [
            ['key' => 'site_name', 'value' => 'MV Rental'],
            ['key' => 'site_description', 'value' => 'Premium vehicle & RV rental marketplace'],
            ['key' => 'default_locale', 'value' => 'en'],
            ['key' => 'supported_locales', 'value' => json_encode(['en', 'es'])],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
