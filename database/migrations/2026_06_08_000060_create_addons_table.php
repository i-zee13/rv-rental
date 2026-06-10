<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('price',10,2)->default(0);
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('addon_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('addon_id')->constrained('addons')->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->unique(['addon_id','locale']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('addon_translations');
        Schema::dropIfExists('addons');
    }
};
