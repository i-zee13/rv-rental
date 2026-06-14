<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('vehicle_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('vehicle_category_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_category_id');
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unique(['vehicle_category_id','locale']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicle_category_translations');
        Schema::dropIfExists('vehicle_categories');
    }
};
