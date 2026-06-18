<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('seo_metas') || ! Schema::hasColumn('seo_metas', 'entity_type')) {
            return;
        }

        // Entity rows must not reuse page-level keys like "vehicles.show" (unique on page_key+locale).
        foreach (DB::table('seo_metas')
            ->whereNotNull('entity_type')
            ->whereNotNull('entity_id')
            ->get() as $row) {
            $key = "entity:{$row->entity_type}:{$row->entity_id}";
            DB::table('seo_metas')->where('id', $row->id)->update(['page_key' => $key]);
        }
    }

    public function down(): void
    {
        // Non-destructive — page_key values are not reverted.
    }
};
