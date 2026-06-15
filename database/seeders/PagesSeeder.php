<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PagesSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $aboutId = DB::table('pages')->insertGetId(['slug'=>'about-us','is_published'=>true,'created_at'=>$now,'updated_at'=>$now]);
        DB::table('page_translations')->insert([
            ['page_id'=>$aboutId,'locale'=>'en','title'=>'About Us','content'=>'<p>About our rental platform demo.</p>','meta_title'=>'About Us - MV Rental','meta_description'=>'About MV Rental'],
            ['page_id'=>$aboutId,'locale'=>'es','title'=>'Sobre Nosotros','content'=>'<p>Acerca de nuestra plataforma de alquiler (demo).</p>','meta_title'=>'Sobre Nosotros - MV Rental','meta_description'=>'Sobre MV Rental'],
        ]);

        $termsId = DB::table('pages')->insertGetId(['slug'=>'terms','is_published'=>true,'created_at'=>$now,'updated_at'=>$now]);
        DB::table('page_translations')->insert([
            ['page_id'=>$termsId,'locale'=>'en','title'=>'Terms & Conditions','content'=>'<p>Terms...</p>','meta_title'=>'Terms - MV Rental','meta_description'=>'Terms'],
            ['page_id'=>$termsId,'locale'=>'es','title'=>'Términos y Condiciones','content'=>'<p>Términos...</p>','meta_title'=>'Términos - MV Rental','meta_description'=>'Términos'],
        ]);
    }
}
