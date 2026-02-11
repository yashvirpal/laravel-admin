<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('short_description')->nullable();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->integer('position')->default(0);
            $table->string('custom_field')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('company')->nullable();
            $table->text('message');
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->string('custom_field')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
        });

        Schema::create('global_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->string('template')->nullable();
            $table->unsignedBigInteger('page_id')->nullable();
            $table->string('custom_field')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('attachment')->nullable();
            $table->boolean('status')->default(0);
            $table->string('custom_field')->nullable();
            $table->string('referrer_url')->nullable();
            $table->string('user_agent')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
        });

        Schema::create('newsletters', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable(); // Optional
            $table->boolean('is_subscribed')->default(true);
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('pages')->onDelete('cascade');
            $table->string('template')->default('default');
            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('banner')->nullable();
            $table->string('alt')->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('seo_image')->nullable();
            $table->string('canonical_url')->nullable();

            $table->boolean('status')->default(true);
            $table->foreignId('author_id')->nullable()->constrained('admins')->nullOnDelete();

            $table->string('custom_field')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('global_sections');
        Schema::dropIfExists('contact_submissions');
        Schema::dropIfExists('newsletters');
        Schema::dropIfExists('pages');
    }
};
