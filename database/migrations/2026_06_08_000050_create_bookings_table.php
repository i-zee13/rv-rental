<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('pickup_location_id')->nullable();
            $table->unsignedBigInteger('return_location_id')->nullable();
            $table->dateTime('pickup_at');
            $table->dateTime('return_at');
            $table->json('extras')->nullable();
            $table->decimal('subtotal',10,2)->default(0);
            $table->decimal('taxes',10,2)->default(0);
            $table->decimal('total',10,2)->default(0);
            $table->string('currency',3)->default('USD');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('booking_addons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('addon_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price',10,2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_addons');
        Schema::dropIfExists('bookings');
    }
};
