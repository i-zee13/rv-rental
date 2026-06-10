<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        DB::table('roles')->insert([
            ['name'=>'super_admin','display_name'=>'Super Admin','permissions'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['name'=>'admin','display_name'=>'Admin','permissions'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['name'=>'manager','display_name'=>'Manager','permissions'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['name'=>'content_editor','display_name'=>'Content Editor','permissions'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['name'=>'booking_agent','display_name'=>'Booking Agent','permissions'=>null,'created_at'=>$now,'updated_at'=>$now],
        ]);
    }
}
