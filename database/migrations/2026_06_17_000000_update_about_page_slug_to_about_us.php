<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pages')->where('slug', 'about')->update(['slug' => 'about-us']);
    }

    public function down(): void
    {
        DB::table('pages')->where('slug', 'about-us')->update(['slug' => 'about']);
    }
};
