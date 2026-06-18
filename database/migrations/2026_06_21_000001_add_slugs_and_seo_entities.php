<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicles', 'slug')) {
                $table->string('slug', 191)->nullable()->after('model');
                $table->unique('slug');
            }
        });

        Schema::table('properties', function (Blueprint $table) {
            if (! Schema::hasColumn('properties', 'slug')) {
                $table->string('slug', 191)->nullable()->after('reference');
                $table->unique('slug');
            }
        });

        Schema::table('seo_metas', function (Blueprint $table) {
            if (! Schema::hasColumn('seo_metas', 'entity_type')) {
                $table->string('entity_type', 50)->nullable()->after('page_key');
                $table->unsignedBigInteger('entity_id')->nullable()->after('entity_type');
                $table->index(['entity_type', 'entity_id', 'locale'], 'seo_metas_entity_locale_index');
            }
        });

        $this->backfillVehicleSlugs();
        $this->backfillPropertySlugs();
    }

    public function down(): void
    {
        Schema::table('seo_metas', function (Blueprint $table) {
            if (Schema::hasColumn('seo_metas', 'entity_type')) {
                $table->dropIndex('seo_metas_entity_locale_index');
                $table->dropColumn(['entity_type', 'entity_id']);
            }
        });

        Schema::table('vehicles', function (Blueprint $table) {
            if (Schema::hasColumn('vehicles', 'slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }
        });

        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }
        });
    }

    protected function backfillVehicleSlugs(): void
    {
        if (! Schema::hasTable('vehicles')) {
            return;
        }

        $used = [];

        foreach (DB::table('vehicles')->orderBy('id')->get() as $row) {
            if (! empty($row->slug)) {
                $used[$row->slug] = true;

                continue;
            }

            $base = Str::slug(trim(($row->make ?? '').'-'.($row->model ?? '')));
            if ($base === '') {
                $base = 'vehicle-'.$row->id;
            }

            $slug = $base;
            $i = 1;
            while (isset($used[$slug])) {
                $slug = $base.'-'.$i;
                $i++;
            }

            $used[$slug] = true;
            DB::table('vehicles')->where('id', $row->id)->update(['slug' => $slug]);
        }
    }

    protected function backfillPropertySlugs(): void
    {
        if (! Schema::hasTable('properties') || ! Schema::hasTable('property_translations')) {
            return;
        }

        $used = [];

        foreach (DB::table('properties')->orderBy('id')->get() as $row) {
            if (! empty($row->slug)) {
                $used[$row->slug] = true;

                continue;
            }

            $title = DB::table('property_translations')
                ->where('property_id', $row->id)
                ->where('locale', 'en')
                ->value('title');

            $base = Str::slug($title ?: ($row->reference ?: 'property-'.$row->id));
            if ($base === '') {
                $base = 'property-'.$row->id;
            }

            $slug = $base;
            $i = 1;
            while (isset($used[$slug])) {
                $slug = $base.'-'.$i;
                $i++;
            }

            $used[$slug] = true;
            DB::table('properties')->where('id', $row->id)->update(['slug' => $slug]);
        }
    }
};
