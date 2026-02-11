<?php
// database/migrations/xxxx_create_carts_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_total', 10, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_total', 10, 2)->default(0);
            $table->string('shipping_method')->nullable();
            $table->string('shipping_label')->nullable();
            $table->decimal('shipping_total', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            
            // Reference product_variants table (not variants)
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
            
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->index(['cart_id', 'product_id']);
            $table->unique(['cart_id', 'product_id', 'variant_id']);
        });

        Schema::create('cart_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['cart_id', 'coupon_id']);
            $table->unique(['cart_id', 'coupon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_coupons');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};