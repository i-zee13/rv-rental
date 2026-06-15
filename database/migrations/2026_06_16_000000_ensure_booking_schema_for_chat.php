<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable()->index();
                $table->string('phone')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('bookings')) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'start_date')) {
                $table->date('start_date')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'end_date')) {
                $table->date('end_date')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'pickup_location')) {
                $table->string('pickup_location')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'dropoff_location')) {
                $table->string('dropoff_location')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'first_name')) {
                $table->string('first_name')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'last_name')) {
                $table->string('last_name')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'phone')) {
                $table->string('phone')->nullable();
            }
        });

        if (!Schema::hasTable('booking_addons')) {
            Schema::create('booking_addons', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('booking_id');
                $table->unsignedBigInteger('addon_id');
                $table->integer('quantity')->default(1);
                $table->decimal('price', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ai_logs')) {
            Schema::create('ai_logs', function (Blueprint $table) {
                $table->id();
                $table->string('action')->nullable();
                $table->text('prompt')->nullable();
                $table->text('response')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Non-destructive safety migration.
    }
};
