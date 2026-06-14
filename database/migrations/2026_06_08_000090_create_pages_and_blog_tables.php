<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('page_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id');
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->unique(['page_id','locale']);
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('author_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_post_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blog_post_id');
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->text('excerpt')->nullable();
            $table->text('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->unique(['blog_post_id','locale']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_post_translations');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('page_translations');
        Schema::dropIfExists('pages');
    }
};
