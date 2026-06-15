<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('property_type_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_type_id');
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unique(['property_type_id', 'locale']);
        });

        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_type_id')->nullable();
            $table->string('reference')->nullable()->unique();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->default('Miami');
            $table->string('state')->default('FL');
            $table->string('zip')->nullable();
            $table->string('neighborhood')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedTinyInteger('bedrooms')->default(1);
            $table->decimal('bathrooms', 3, 1)->default(1);
            $table->unsignedInteger('sqft')->nullable();
            $table->unsignedSmallInteger('max_guests')->nullable();
            $table->unsignedSmallInteger('min_nights')->default(1);
            $table->decimal('price_per_month', 10, 2)->default(0);
            $table->decimal('price_per_week', 10, 2)->nullable();
            $table->decimal('price_per_night', 10, 2)->nullable();
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->decimal('cleaning_fee', 10, 2)->default(0);
            $table->boolean('featured')->default(false);
            $table->boolean('instant_book')->default(false);
            $table->boolean('pets_allowed')->default(false);
            $table->boolean('furnished')->default(false);
            $table->json('amenities')->nullable();
            $table->date('available_from')->nullable();
            $table->string('status')->default('available');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('property_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('highlights')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->unique(['property_id', 'locale']);
        });

        Schema::create('property_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->string('path');
            $table->string('alt_text')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->nullable()->after('vehicle_id');
            $table->string('property_name')->nullable()->after('property_id');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['property_id', 'property_name']);
        });
        Schema::dropIfExists('property_images');
        Schema::dropIfExists('property_translations');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('property_type_translations');
        Schema::dropIfExists('property_types');
    }
};
