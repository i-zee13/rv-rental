<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolesTableSeeder::class,
            UsersTableSeeder::class,
            VehicleCategoriesSeeder::class,
            PropertyTypesSeeder::class,
            LocationsSeeder::class,
            VehiclesSeeder::class,
            PropertiesSeeder::class,
            AddonSeeder::class,
            SeoSeeder::class,
            SettingsSeeder::class,
        ]);
    }
}
