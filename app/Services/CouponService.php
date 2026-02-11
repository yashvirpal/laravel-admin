<?php
// app/Services/CouponService.php

namespace App\Services;

use App\Models\Coupon;
use Carbon\Carbon;

class CouponService
{
    protected ?Coupon $coupon = null;

    /**
     * Load coupon by code
     */
    public function load(string $code): void
    {
        $this->coupon = Coupon::with(['rules', 'actions'])
            ->where('code', $code)
            ->first();
    }

    /**
     * Get loaded coupon
     */
    public function getCoupon(): ?Coupon
    {
        return $this->coupon;
    }

    /**
     * Validate if coupon is valid
     */
    public function isValid(): bool
    {
        if (!$this->coupon) {
            return false;
        }

        // Check if coupon is active
        if (!$this->coupon->status) {
            return false;
        }

        // Check start date
        if ($this->coupon->starts_at && Carbon::parse($this->coupon->starts_at)->isFuture()) {
            return false;
        }

        // Check expiration date
        if ($this->coupon->expires_at && Carbon::parse($this->coupon->expires_at)->isPast()) {
            return false;
        }

        // Check usage limit
        if ($this->coupon->usage_limit && $this->coupon->used_count >= $this->coupon->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Get validation error message
     */
    public function getValidationError(): string
    {
        if (!$this->coupon) {
            return 'Coupon not found.';
        }

        if (!$this->coupon->status) {
            return 'This coupon is inactive.';
        }

        if ($this->coupon->starts_at && Carbon::parse($this->coupon->starts_at)->isFuture()) {
            return 'This coupon is not yet active. Available from ' . Carbon::parse($this->coupon->starts_at)->format('M d, Y');
        }

        if ($this->coupon->expires_at && Carbon::parse($this->coupon->expires_at)->isPast()) {
            return 'This coupon has expired on ' . Carbon::parse($this->coupon->expires_at)->format('M d, Y');
        }

        if ($this->coupon->usage_limit && $this->coupon->used_count >= $this->coupon->usage_limit) {
            return 'This coupon has reached its usage limit.';
        }

        return 'This coupon is invalid.';
    }

    /**
     * Check if cart meets coupon rules
     */
    public function meetsRules(array $cartItems): bool
    {
        if (!$this->coupon || !$this->coupon->rules) {
            return false;
        }

        // If no rules, coupon applies to all
        if ($this->coupon->rules->isEmpty()) {
            return true;
        }

        foreach ($this->coupon->rules as $rule) {
            $meets = $this->checkRule($rule, $cartItems);
            if (!$meets) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check individual rule
     */
    protected function checkRule($rule, array $cartItems): bool
    {
        switch ($rule->condition) {
            case 'product':
                // Check if specific product is in cart
                foreach ($cartItems as $item) {
                    if ($item['product_id'] == $rule->product_id) {
                        return true;
                    }
                }
                return false;

            case 'category':
                // Check if any product from category is in cart
                foreach ($cartItems as $item) {
                    if ($item['category_id'] == $rule->category_id) {
                        return true;
                    }
                }
                return false;

            case 'cart_subtotal':
                // Check if cart subtotal meets minimum
                $subtotal = array_sum(array_map(fn($item) => $item['price'] * $item['qty'], $cartItems));
                return $subtotal >= $rule->min_value;

            case 'cart_quantity':
                // Check if cart quantity meets minimum
                $totalQty = array_sum(array_column($cartItems, 'qty'));
                return $totalQty >= $rule->min_qty;

            default:
                return false;
        }
    }

    /**
     * Apply coupon and calculate discount
     */
    public function apply(array $cartItems): array
    {
        if (!$this->coupon) {
            return [
                'success' => false,
                'message' => 'Coupon not loaded.',
                'discount' => 0,
                'free_items' => []
            ];
        }

        // Validate coupon
        if (!$this->isValid()) {
            return [
                'success' => false,
                'message' => $this->getValidationError(),
                'discount' => 0,
                'free_items' => []
            ];
        }

        // Check if cart meets rules
        if (!$this->meetsRules($cartItems)) {
            return [
                'success' => false,
                'message' => 'Your cart does not meet the requirements for this coupon.',
                'discount' => 0,
                'free_items' => []
            ];
        }

        $discount = 0;
        $freeItems = [];

        // Calculate discount based on actions
        foreach ($this->coupon->actions as $action) {
            switch ($action->action) {
                case 'fixed_discount':
                    $discount += $this->calculateFixedDiscount($action, $cartItems);
                    break;

                case 'percentage_discount':
                    $discount += $this->calculatePercentageDiscount($action, $cartItems);
                    break;

                case 'free_product':
                    $freeItems = array_merge($freeItems, $this->getFreeProducts($action));
                    break;

                case 'discount_product':
                    $discount += $this->calculateProductDiscount($action, $cartItems);
                    break;

                case 'bogo':
                    $bogo = $this->calculateBogo($action, $cartItems);
                    $freeItems = array_merge($freeItems, $bogo['free_items']);
                    break;
            }
        }

        return [
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'discount' => $discount,
            'free_items' => $freeItems
        ];
    }

    /**
     * Calculate fixed discount
     */
    protected function calculateFixedDiscount($action, array $cartItems): float
    {
        return (float) $action->value;
    }

    /**
     * Calculate percentage discount
     */
    protected function calculatePercentageDiscount($action, array $cartItems): float
    {
        $subtotal = array_sum(array_map(fn($item) => $item['price'] * $item['qty'], $cartItems));
        return ($subtotal * $action->value) / 100;
    }

    /**
     * Get free products
     */
    protected function getFreeProducts($action): array
    {
        return [
            [
                'product_id' => $action->product_id,
                'quantity' => $action->quantity ?? 1
            ]
        ];
    }

    /**
     * Calculate product-specific discount
     */
    protected function calculateProductDiscount($action, array $cartItems): float
    {
        $discount = 0;

        foreach ($cartItems as $item) {
            if ($item['product_id'] == $action->product_id) {
                $itemTotal = $item['price'] * $item['qty'];
                $discount += ($itemTotal * $action->value) / 100;
            }
        }

        return $discount;
    }

    /**
     * Calculate BOGO (Buy X Get Y)
     */
    protected function calculateBogo($action, array $cartItems): array
    {
        $freeItems = [];

        foreach ($cartItems as $item) {
            if ($item['product_id'] == $action->product_id) {
                $buyQty = $action->buy_qty ?? 1;
                $getQty = $action->get_qty ?? 1;
                $itemQty = $item['qty'];

                // Calculate how many free items
                $freeCount = floor($itemQty / $buyQty) * $getQty;

                if ($freeCount > 0) {
                    $freeItems[] = [
                        'product_id' => $action->product_id,
                        'quantity' => $freeCount
                    ];
                }
            }
        }

        return [
            'discount' => 0,
            'free_items' => $freeItems
        ];
    }

    /**
     * Mark coupon as used
     */
    public function markUsed(): void
    {
        if ($this->coupon) {
            $this->coupon->increment('used_count');
        }
    }
}