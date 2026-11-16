<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE vehicle_images MODIFY path VARCHAR(500) NOT NULL');
        DB::statement('ALTER TABLE property_images MODIFY path VARCHAR(500) NOT NULL');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE vehicle_images MODIFY path VARCHAR(191) NOT NULL');
        DB::statement('ALTER TABLE property_images MODIFY path VARCHAR(191) NOT NULL');
    }
};
