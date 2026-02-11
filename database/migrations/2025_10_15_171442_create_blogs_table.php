<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('blog_categories')->onDelete('cascade');
            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('banner')->nullable();
            $table->string('banner_alt')->nullable();

            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('seo_image')->nullable();
            $table->string('canonical_url')->nullable();

            $table->boolean('status')->default(true)->default(1);
            $table->foreignId('author_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->boolean('is_featured')->default(false);

            $table->string('custom_field')->nullable();

            $table->timestamps();
        });

        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('blog_tags')->onDelete('cascade');
            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('banner')->nullable();
            $table->string('banner_alt')->nullable();

            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('seo_image')->nullable();
            $table->string('canonical_url')->nullable();

            $table->boolean('status')->default(true)->default(1);
            $table->foreignId('author_id')->nullable()->constrained('admins')->nullOnDelete();

            $table->string('custom_field')->nullable();

            $table->timestamps();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();

            // Remove: $table->foreignId('category_id')->nullable()->constrained('blog_categories')->nullOnDelete();

            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();

            $table->string('banner')->nullable();
            $table->string('banner_alt')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();

            // SEO fields
            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('seo_image')->nullable();
            $table->string('canonical_url')->nullable();

            $table->string('custom_field')->nullable();

            // ✅ New flag
            $table->boolean('is_featured')->default(false);

            $table->boolean('status')->default(true);
            $table->foreignId('author_id')->nullable()->constrained('admins')->nullOnDelete();

            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });


        // blog_post_category
        Schema::create('blog_post_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('blog_categories')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['post_id', 'category_id']);
        });

        // blog_post_tag
        Schema::create('blog_post_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('blog_tags')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['post_id', 'tag_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_post_category');
        Schema::dropIfExists('blog_post_tag');
        Schema::dropIfExists('blog_posts');
    }
};
