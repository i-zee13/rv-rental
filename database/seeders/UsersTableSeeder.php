<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        $email = 'admin@example.com';

        if (DB::table('users')->where('email', $email)->exists()) {
            return;
        }

        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'preferred_language' => 'en',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
