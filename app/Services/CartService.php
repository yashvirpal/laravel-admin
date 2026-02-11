<?php
// app/Services/CartService.php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartService
{
    protected Cart $cart;
    protected CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }



    /**
     * Merge guest cart into user cart after login

 */
    public function mergeGuestCart(?string $guestSessionId, int $userId): void
    {
        if (!$guestSessionId) {
            Log::info('No guest session ID provided for merge');
            return;
        }

        DB::beginTransaction();
        try {
            // Get guest cart
            $guestCart = Cart::with(['items.product.categories', 'coupons'])
                ->where('session_id', $guestSessionId)
                ->whereNull('user_id')
                ->first();

            if (!$guestCart || $guestCart->items->isEmpty()) {
                Log::info('No guest cart found to merge', [
                    'session_id' => $guestSessionId
                ]);
                DB::commit();
                return;
            }

            Log::info('Found guest cart to merge', [
                'guest_cart_id' => $guestCart->id,
                'items_count' => $guestCart->items->count(),
                'coupons_count' => $guestCart->coupons->count()
            ]);

            // Get or create user cart
            $userCart = Cart::with(['items', 'coupons'])
                ->firstOrCreate(
                    ['user_id' => $userId],
                    ['session_id' => $guestSessionId]
                );

            Log::info('User cart loaded', [
                'user_cart_id' => $userCart->id,
                'existing_items' => $userCart->items->count()
            ]);

            // Merge items
            foreach ($guestCart->items as $guestItem) {
                // Check if user cart already has this item
                $existingItem = $userCart->items()
                    ->where('product_id', $guestItem->product_id)
                    ->where('variant_id', $guestItem->variant_id)
                    ->first();

                if ($existingItem) {
                    // Update quantity
                    $oldQty = $existingItem->quantity;
                    $existingItem->quantity += $guestItem->quantity;
                    $existingItem->save();

                    Log::info('Updated existing item', [
                        'product_id' => $guestItem->product_id,
                        'variant_id' => $guestItem->variant_id,
                        'old_qty' => $oldQty,
                        'added_qty' => $guestItem->quantity,
                        'new_qty' => $existingItem->quantity
                    ]);
                } else {
                    // Create new item
                    $userCart->items()->create([
                        'product_id' => $guestItem->product_id,
                        'variant_id' => $guestItem->variant_id,
                        'quantity' => $guestItem->quantity,
                        'price' => $guestItem->price,
                    ]);

                    Log::info('Created new item', [
                        'product_id' => $guestItem->product_id,
                        'variant_id' => $guestItem->variant_id,
                        'quantity' => $guestItem->quantity,
                        'price' => $guestItem->price
                    ]);
                }
            }

            // Merge coupons
            foreach ($guestCart->coupons as $coupon) {
                if (!$userCart->coupons()->where('coupon_id', $coupon->id)->exists()) {
                    $userCart->coupons()->attach($coupon->id, [
                        'discount_amount' => $coupon->pivot->discount_amount
                    ]);

                    Log::info('Merged coupon', [
                        'coupon_id' => $coupon->id,
                        'code' => $coupon->code,
                        'discount' => $coupon->pivot->discount_amount
                    ]);
                }
            }

            // Delete guest cart
            $guestCart->items()->delete();
            $guestCart->coupons()->detach();
            $guestCart->delete();

            Log::info('Guest cart deleted', ['guest_cart_id' => $guestCart->id]);

            // Reload user cart relationships
            $userCart->load(['items.product.categories', 'items.variant', 'coupons']);

            // Set merged cart as current cart
            $this->cart = $userCart;

            // Recalculate totals
            $this->recalculate();

            DB::commit();

            Log::info('✅ Cart merge completed successfully', [
                'user_id' => $userId,
                'user_cart_id' => $userCart->id,
                'final_items_count' => $userCart->items->count(),
                'final_coupons_count' => $userCart->coupons->count()
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('❌ Cart merge failed', [
                'user_id' => $userId,
                'session_id' => $guestSessionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get or create cart for user/session
     */
    public function getCart(?int $userId = null, ?string $sessionId = null): Cart
    {
        $query = Cart::with([
            'items.product.categories',
            'items.variant.values.attribute',
            'coupons'
        ]);

        if ($userId) {
            // Check if there's a guest cart to merge
            $guestCart = Cart::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->first();

            if ($guestCart && $guestCart->items()->count() > 0) {
                // Merge guest cart into user cart
                $this->mergeGuestCart($sessionId, $userId);
            }

            $this->cart = $query->firstOrCreate(
                ['user_id' => $userId],
                ['session_id' => $sessionId]
            );
        } else {
            $this->cart = $query->firstOrCreate(
                ['session_id' => $sessionId],
                ['user_id' => null]
            );
        }

        return $this->cart;
    }

    /**
     * Add item to cart
     */
    public function addItem(int $productId, int $quantity = 1, ?int $variantId = null, float $price): CartItem
    {
        DB::beginTransaction();
        try {
            $existingItem = $this->cart->items()
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            if ($existingItem) {
                $existingItem->quantity = $existingItem->quantity + $quantity;
                $existingItem->save();
                $item = $existingItem;
            } else {
                $item = $this->cart->items()->create([
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);
            }

            $this->recalculate();

            DB::commit();
            return $item->fresh();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to add item to cart', [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $price,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Update item quantity
     */
    public function updateItem(int $itemId, int $quantity): bool
    {
        DB::beginTransaction();
        try {
            $item = $this->cart->items()->findOrFail($itemId);

            if ($quantity <= 0) {
                return $this->removeItem($itemId);
            }

            $item->quantity = $quantity;
            $item->save();

            $this->recalculate();

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to update cart item', [
                'item_id' => $itemId,
                'quantity' => $quantity,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $itemId): bool
    {
        DB::beginTransaction();
        try {
            $this->cart->items()->where('id', $itemId)->delete();
            $this->recalculate();

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to remove cart item', [
                'item_id' => $itemId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Apply coupon to cart
     */
    public function applyCoupon(string $code): array
    {
        DB::beginTransaction();
        try {
            // Load and validate coupon
            $this->couponService->load($code);

            if (!$this->couponService->isValid()) {
                return [
                    'success' => false,
                    'message' => $this->couponService->getValidationError(),
                ];
            }

            $coupon = $this->couponService->getCoupon();

            // Check if coupon already applied
            if ($this->cart->coupons()->where('coupon_id', $coupon->id)->exists()) {
                return [
                    'success' => false,
                    'message' => 'Coupon already applied to cart.',
                ];
            }

            // Prepare cart items for coupon validation
            $cartItems = $this->cart->items->map(function ($item) {
                $categoryId = $item->product->categories->first()?->id ?? null;

                return [
                    'product_id' => $item->product_id,
                    'category_id' => $categoryId,
                    'price' => $item->price,
                    'qty' => $item->quantity,
                ];
            })->toArray();

            // Apply coupon
            $result = $this->couponService->apply($cartItems);

            if (!$result['success']) {
                DB::rollBack();
                return $result;
            }

            // Attach coupon to cart with discount amount
            $this->cart->coupons()->attach($coupon->id, [
                'discount_amount' => $result['discount']
            ]);

            // CRITICAL: Reload coupons relationship after attaching
            $this->cart->load('coupons');

            // Handle free items
            if (!empty($result['free_items'])) {
                foreach ($result['free_items'] as $freeItem) {
                    $product = Product::find($freeItem['product_id']);
                    if ($product) {
                        $this->cart->items()->create([
                            'product_id' => $freeItem['product_id'],
                            'variant_id' => null,
                            'quantity' => $freeItem['quantity'],
                            'price' => 0,
                        ]);
                    }
                }

                // Reload items if we added free items
                $this->cart->load('items.product.categories');
            }

            // Recalculate with new discount
            $this->recalculate();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Coupon applied successfully.',
                'discount' => $result['discount'],
                'free_items' => $result['free_items'],
                'coupon_id' => $coupon->id,
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to apply coupon', [
                'cart_id' => $this->cart->id,
                'code' => $code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to apply coupon. Please try again.',
            ];
        }
    }

    /**
     * Remove coupon from cart
     */
    public function removeCoupon(int $couponId): bool
    {
        DB::beginTransaction();
        try {
            // Remove free items added by coupons (price = 0)
            $this->cart->items()->where('price', 0)->delete();

            // Detach the coupon
            $this->cart->coupons()->detach($couponId);

            // CRITICAL: Reload coupons relationship after detaching
            $this->cart->load('coupons');

            // Reload items in case we deleted free items
            $this->cart->load('items.product.categories');

            // Recalculate totals
            $this->recalculate();

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to remove coupon', [
                'coupon_id' => $couponId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Recalculate cart totals including all coupons
     */
    public function recalculate(): void
    {
        Log::info('=== RECALCULATE CART ===', ['cart_id' => $this->cart->id]);

        // Calculate subtotal (excluding free items)
        $this->cart->subtotal = $this->cart->items()
            ->where('price', '>', 0)
            ->get()
            ->sum(fn($item) => $item->price * $item->quantity);

        Log::info('Subtotal calculated', ['subtotal' => $this->cart->subtotal]);

        // Calculate total discount from all coupons
        $totalDiscount = 0;

        Log::info('Processing coupons', ['coupon_count' => $this->cart->coupons->count()]);

        foreach ($this->cart->coupons as $coupon) {
            Log::info('Processing coupon', [
                'coupon_id' => $coupon->id,
                'coupon_code' => $coupon->code
            ]);

            $this->couponService->load($coupon->code);

            $cartItems = $this->cart->items()
                ->where('price', '>', 0)
                ->get()
                ->map(function ($item) {
                    $categoryId = $item->product->categories->first()?->id ?? null;

                    return [
                        'product_id' => $item->product_id,
                        'category_id' => $categoryId,
                        'price' => $item->price,
                        'qty' => $item->quantity,
                    ];
                })->toArray();

            $result = $this->couponService->apply($cartItems);

            if ($result['success']) {
                $totalDiscount += $result['discount'];

                Log::info('Coupon discount calculated', [
                    'coupon_id' => $coupon->id,
                    'discount' => $result['discount'],
                    'total_discount' => $totalDiscount
                ]);

                // Update pivot discount amount
                $this->cart->coupons()->updateExistingPivot($coupon->id, [
                    'discount_amount' => $result['discount']
                ]);
            }
        }

        // Ensure discount doesn't exceed subtotal
        $this->cart->discount_total = min($totalDiscount, $this->cart->subtotal);

        Log::info('Final discount', ['discount_total' => $this->cart->discount_total]);

        // Calculate tax (on discounted amount)
        $taxableAmount = $this->cart->subtotal - $this->cart->discount_total;
        $this->cart->tax_total = ($taxableAmount * $this->cart->tax_rate) / 100;

        // Calculate grand total
        $this->cart->grand_total = $this->cart->subtotal
            - $this->cart->discount_total
            + $this->cart->tax_total
            + $this->cart->shipping_total;

        Log::info('Cart totals calculated', [
            'subtotal' => $this->cart->subtotal,
            'discount' => $this->cart->discount_total,
            'tax' => $this->cart->tax_total,
            'shipping' => $this->cart->shipping_total,
            'grand_total' => $this->cart->grand_total
        ]);

        $this->cart->save();

        Log::info('=== RECALCULATE COMPLETE ===');
    }

    /**
     * Set shipping method
     */
    public function setShipping(string $method, string $label, float $cost): void
    {
        $this->cart->shipping_method = $method;
        $this->cart->shipping_label = $label;
        $this->cart->shipping_total = $cost;
        $this->cart->save();

        $this->recalculate();
    }

    /**
     * Clear cart
     */
    public function clear(): void
    {
        $this->cart->items()->delete();
        $this->cart->coupons()->detach();
        $this->cart->update([
            'subtotal' => 0,
            'discount_total' => 0,
            'tax_total' => 0,
            'shipping_total' => 0,
            'grand_total' => 0,
        ]);
    }

    /**
     * Mark all coupons as used (call after successful checkout)
     */
    public function markCouponsUsed(): void
    {
        foreach ($this->cart->coupons as $coupon) {
            $coupon->increment('used_count');
        }
    }

    /**
     * Get cart count
     */
    public function count(): int
    {
        return $this->cart->items->sum('quantity');
    }
}