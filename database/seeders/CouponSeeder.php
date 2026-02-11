<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use App\Models\CouponRule;
use App\Models\CouponAction;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        // -----------------------------
        // 1. Fixed discount coupon
        // -----------------------------
        $this->createCoupon(
            'Flat 200 OFF',
            'FLAT200',
            'cart_subtotal',
            ['min_value' => 500],
            'fixed_discount',
            ['value' => 200]
        );

        // -----------------------------
        // 2. Percentage discount coupon
        // -----------------------------
        $this->createCoupon(
            '10% Off',
            'SAVE10',
            'cart_subtotal',
            ['min_value' => 1000],
            'percentage_discount',
            ['value' => 10]
        );

        // -----------------------------
        // 3. Buy 1 Get 1 Free (Same product)
        // -----------------------------
        $this->createCoupon(
            'BOGO - Product 1',
            'BOGO2026',
            'product',
            ['product_id' => 1, 'min_qty' => 1],
            'bogo',
            ['product_id' => 1, 'buy_qty' => 1, 'get_qty' => 1]
        );

        // -----------------------------
        // 4. Buy Product 1 Get Product 2 Free
        // -----------------------------
        $this->createCoupon(
            'Buy 1 Get Product 2 Free',
            'BUY1GET2',
            'product',
            ['product_id' => 1, 'min_qty' => 1],
            'free_product',
            ['product_id' => 2, 'quantity' => 1]
        );

        // -----------------------------
        // 5. Discount specific product
        // -----------------------------
        $this->createCoupon(
            '50% Off Product 3',
            'PROD3DISCOUNT',
            'product',
            ['product_id' => 3, 'min_qty' => 1],
            'discount_product',
            ['product_id' => 3, 'value' => 50]
        );

        // -----------------------------
        // 6. Cart quantity based coupon
        // -----------------------------
        $this->createCoupon(
            'Buy 10 items Get 500 OFF',
            'BUY10GET500',
            'cart_quantity',
            ['min_qty' => 10],
            'fixed_discount',
            ['value' => 500]
        );
    }

    /**
     * Helper function to create coupon, rule and action
     */
    private function createCoupon(
        string $title,
        string $code,
        string $ruleCondition,
        array $ruleData,
        string $actionType,
        array $actionData
    ) {
        $coupon = Coupon::create([
            'title' => $title,
            'code' => $code,
            'status' => 1,
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'usage_limit' => $ruleData['usage_limit'] ?? null,
        ]);

        // Create Rule
        $coupon->rules()->create(array_merge([
            'condition' => $ruleCondition,
            'product_id' => $ruleData['product_id'] ?? null,
            'category_id' => $ruleData['category_id'] ?? null,
            'min_value' => $ruleData['min_value'] ?? null,
            'min_qty' => $ruleData['min_qty'] ?? null,
        ], $ruleData));

        // Create Action
        $coupon->actions()->create(array_merge([
            'action' => $actionType,
            'product_id' => $actionData['product_id'] ?? null,
            'value' => $actionData['value'] ?? null,
            'quantity' => $actionData['quantity'] ?? null,
            'buy_qty' => $actionData['buy_qty'] ?? null,
            'get_qty' => $actionData['get_qty'] ?? null,
        ], $actionData));
    }
}
