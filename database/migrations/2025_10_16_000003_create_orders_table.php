<?php
// database/migrations/xxxx_create_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Orders table
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Customer info (for guest checkout)
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();

            // Address references
            $table->foreignId('billing_address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->nullOnDelete();

            // Legacy address fields (keep for backward compatibility)
            $table->text('shipping_address')->nullable();
            $table->text('billing_address')->nullable();

            // Pricing
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_total', 10, 2)->default(0); // Changed from 'discount'
            $table->decimal('tax_total', 10, 2)->default(0); // Changed from 'tax'
            $table->decimal('shipping_total', 10, 2)->default(0); // Added
            $table->decimal('total', 10, 2)->default(0);

            // Shipping & Payment
            $table->string('shipping_method')->nullable();
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');

            // Order status
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled', 'refunded'])->default('pending');

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();
        });

        // Order items table
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();

            // Product details (snapshot at time of order)
            $table->string('product_name');
            $table->string('variant_name')->nullable();
            $table->string('sku')->nullable();

            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2); // Unit price
            $table->decimal('subtotal', 10, 2); // price * quantity

            $table->timestamps();
        });

        // Order coupons table (track which coupons were used)
        Schema::create('order_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->string('code'); // Store code in case coupon is deleted
            $table->decimal('discount_amount', 10, 2);
            $table->timestamps();
        });

        // Transactions table (keep your existing structure)
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('transaction_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'upi', 'wallet', 'cod'])->default('cash');
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->text('response_data')->nullable(); // Store gateway response
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('order_coupons');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};