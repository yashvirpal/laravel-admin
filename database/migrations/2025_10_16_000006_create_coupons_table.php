<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique()->index();
            $table->boolean('status')->default(1)->index();

            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable();

            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);

            $table->timestamps();
        });

        Schema::create('coupon_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();

            $table->enum('condition', [
                'product',
                'category',
                'cart_subtotal',
                'cart_quantity'
            ]);

            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->unsignedBigInteger('category_id')->nullable()->index();

            $table->decimal('min_value', 10, 2)->nullable(); // e.g. min cart subtotal
            $table->unsignedInteger('min_qty')->nullable();  // e.g. min quantity

            $table->timestamps();
        });

        Schema::create('coupon_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();

            $table->enum('action', [
                'fixed_discount',
                'percentage_discount',
                'free_product',
                'discount_product',
                'bogo'
            ]);

            $table->decimal('value', 10, 2)->nullable(); // only for % or fixed
            $table->unsignedBigInteger('product_id')->nullable()->index(); // target product
            $table->unsignedInteger('quantity')->nullable(); // for free_product
            $table->unsignedInteger('buy_qty')->nullable(); // BOGO buy qty
            $table->unsignedInteger('get_qty')->nullable(); // BOGO get qty

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_actions');
        Schema::dropIfExists('coupon_rules');
        Schema::dropIfExists('coupons');
    }
};
