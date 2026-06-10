<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->string('query')->nullable();
            $table->json('filters')->nullable();
            $table->integer('results_count')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action')->nullable();
            $table->text('prompt')->nullable();
            $table->text('response')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->text('detail')->nullable();
            $table->timestamps();
        });

        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->timestamps();
        });

        Schema::create('email_template_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_template_id')->constrained('email_templates')->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->unique(['email_template_id','locale']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_template_translations');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('ai_logs');
        Schema::dropIfExists('search_logs');
        Schema::dropIfExists('analytics_events');
    }
};
