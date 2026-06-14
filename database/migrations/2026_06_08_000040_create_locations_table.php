<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('branch'); // country, state, city, airport, branch
            $table->string('code')->nullable(); // e.g., airport code
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->decimal('latitude',10,7)->nullable();
            $table->decimal('longitude',10,7)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('opening_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('location_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->unique(['location_id','locale']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('location_translations');
        Schema::dropIfExists('locations');
    }
};
