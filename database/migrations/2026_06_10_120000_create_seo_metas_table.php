<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_metas', function (Blueprint $table) {
            $table->id();
            $table->string('page_key');
            $table->string('locale', 5)->default('en');
            $table->string('label')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_type')->default('website');
            $table->string('twitter_card')->default('summary_large_image');
            $table->string('twitter_site')->nullable();
            $table->string('robots')->default('index,follow');
            $table->string('canonical')->nullable();
            $table->text('schema_json')->nullable();
            $table->string('google_verification')->nullable();
            $table->string('bing_verification')->nullable();
            $table->boolean('noindex')->default(false);
            $table->timestamps();

            $table->unique(['page_key', 'locale']);
        });

        if (Schema::hasTable('vehicle_translations')) {
            Schema::table('vehicle_translations', function (Blueprint $table) {
                if (!Schema::hasColumn('vehicle_translations', 'meta_title')) {
                    $table->string('meta_title')->nullable()->after('title');
                    $table->text('meta_description')->nullable()->after('meta_title');
                    $table->string('meta_keywords')->nullable()->after('meta_description');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vehicle_translations')) {
            Schema::table('vehicle_translations', function (Blueprint $table) {
                foreach (['meta_title', 'meta_description', 'meta_keywords'] as $col) {
                    if (Schema::hasColumn('vehicle_translations', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        Schema::dropIfExists('seo_metas');
    }
};
