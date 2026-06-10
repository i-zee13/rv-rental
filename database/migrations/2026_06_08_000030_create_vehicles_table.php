<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('vehicle_categories')->nullOnDelete();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('year')->nullable();
            $table->string('vin')->nullable()->unique();
            $table->string('internal_id')->nullable()->unique();
            $table->integer('seats')->default(4);
            $table->integer('doors')->default(4);
            $table->integer('bags')->default(2);
            $table->string('transmission')->nullable();
            $table->string('fuel_type')->nullable();
            $table->decimal('price_per_day', 10, 2)->default(0);
            $table->decimal('price_per_week', 10, 2)->nullable();
            $table->decimal('price_per_month', 10, 2)->nullable();
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->decimal('cleaning_fee', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->boolean('featured')->default(false);
            $table->boolean('instant_book')->default(false);
            $table->boolean('delivery_available')->default(false);
            $table->boolean('pet_friendly')->default(false);
            $table->boolean('smoking_allowed')->default(false);
            $table->string('status')->default('available');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('vehicle_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('specs')->nullable();
            $table->unique(['vehicle_id','locale']);
        });

        Schema::create('vehicle_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->string('path');
            $table->string('alt_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicle_images');
        Schema::dropIfExists('vehicle_translations');
        Schema::dropIfExists('vehicles');
    }
};
