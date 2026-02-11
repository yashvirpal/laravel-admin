<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('product_categories')->onDelete('cascade');
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

        Schema::create('product_tags', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('product_tags')->onDelete('cascade');
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

        Schema::create('product_brands', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('sku')->unique()->nullable();

            // Pricing
            $table->decimal('regular_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->nullable();

            // Inventory
            $table->integer('stock')->default(0);
            $table->boolean('has_variants')->default(false);

            // Descriptions
            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();

            // Images
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

            // Misc
            $table->string('custom_field')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_special')->default(false);

            // Status & Author
            $table->boolean('status')->default(true);
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('product_brands')->nullOnDelete();


            $table->timestamps();
        });

        Schema::create('product_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('image'); // filename or path
            $table->string('alt')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Pivot: product-category
        Schema::create('product_category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_category_id')->constrained('product_categories')->cascadeOnDelete();
            $table->unique(['product_id', 'product_category_id']);
            $table->timestamps();
        });

        // Pivot: product-tag
        Schema::create('product_product_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_tag_id')->constrained('product_tags')->cascadeOnDelete();
            $table->unique(['product_id', 'product_tag_id']);
            $table->timestamps();
        });

        // Product Attributes (e.g., Color, Size)
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Size, Color, etc
            $table->string('slug')->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Attribute Values (e.g., Red, Blue, Large, Medium)
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('product_attributes')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Pivot: Assign Attribute to Product (not values)
        Schema::create('product_product_attribute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_attribute_id')->constrained('product_attributes')->cascadeOnDelete();
            $table->unique(['product_id', 'product_attribute_id']);
            $table->timestamps();
        });

        // Product Variants (final SKU combos)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            $table->string('sku')->unique()->nullable();
            $table->decimal('regular_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);

            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();

            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Pivot: Variant Attribute Values (e.g., Variant #1 = Size:Large + Color:Red)
        Schema::create('product_variant_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained('product_attribute_values')->cascadeOnDelete();
            $table->unique(['variant_id', 'attribute_value_id']);
            $table->timestamps();
        });


        Schema::create('product_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('question');
            $table->text('answer');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('product_faqs');
        Schema::dropIfExists('product_variant_values');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_product_attribute');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');

        Schema::dropIfExists('product_galleries');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('product_tags');
        Schema::dropIfExists('product_brands');
        Schema::dropIfExists('product_product_tag');
        Schema::dropIfExists('product_category_product');
        Schema::dropIfExists('products');
    }
};
