<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings','start_date')) {
                $table->date('start_date')->nullable()->after('return_at');
            }
            if (!Schema::hasColumn('bookings','end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('bookings','pickup_location')) {
                $table->string('pickup_location')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('bookings','dropoff_location')) {
                $table->string('dropoff_location')->nullable()->after('pickup_location');
            }
            if (!Schema::hasColumn('bookings','first_name')) {
                $table->string('first_name')->nullable()->after('dropoff_location');
                $table->string('last_name')->nullable()->after('first_name');
                $table->string('email')->nullable()->after('last_name');
                $table->string('phone')->nullable()->after('email');
            }
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $cols = ['start_date','end_date','pickup_location','dropoff_location','first_name','last_name','email','phone'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('bookings',$c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
