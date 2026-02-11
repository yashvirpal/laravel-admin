<?php
// app/Http/Controllers/CartController.php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variant;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {
    }

    /**
     * Display cart page
     */
    public function index()
    {
        $cart = $this->cartService->getCart(
            auth()->id(),
            session()->getId()
        );

        return view('frontend.cart', compact('cart'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request, Product $product)
    {
        try {
            $request->validate([
                'quantity' => 'nullable|integer|min:1|max:100',
                'variant_id' => 'nullable|exists:product_variants,id',
            ]);

            $variant = null;
            $price = 0;

            // Determine price based on product or variant
            if ($request->variant_id) {
                // Get variant and its price
                $variant = $product->variants()->findOrFail($request->variant_id);

                // Priority: variant sale_price > variant regular_price > product sale_price > product regular_price
                $price = $variant->sale_price
                    ?? $variant->regular_price
                    ?? $variant->price
                    ?? $product->sale_price
                    ?? $product->regular_price
                    ?? $product->price
                    ?? 0;
            } else {
                // Get product price
                // Priority: sale_price > regular_price > selling_price > price
                $price = $product->sale_price
                    ?? $product->selling_price
                    ?? $product->regular_price
                    ?? $product->price
                    ?? 0;
            }

            // Ensure price is a float
            $price = (float) $price;

            // Get or create cart
            $cart = $this->cartService->getCart(
                auth()->id(),
                session()->getId()
            );

            // Add item to cart
            $item = $this->cartService->addItem(
                productId: $product->id,
                quantity: $request->quantity ?? 1,
                variantId: $variant?->id,
                price: $price
            );

            return response()->json([
                'status' => true,
                'message' => 'Added to cart successfully',
                'cart_count' => $cart->fresh()->items->count(),
                'cart_total' => number_format($cart->fresh()->grand_total, 2),
                'item' => $item
            ]);
        } catch (\Exception $e) {
            \Log::error('Cart add error', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'message' => 'Could not add item to cart'
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $itemId)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1|max:100'
            ]);

            // Get cart
            $cart = $this->cartService->getCart(
                auth()->id(),
                session()->getId()
            );

            // Update item quantity
            $this->cartService->updateItem($itemId, $request->quantity);

            // Refresh cart
            $cart->refresh();
            $item = $cart->items()->find($itemId);

            if (!$item) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item not found in cart'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'cart_count' => $cart->items->count(),
                'item_subtotal' => number_format($item->line_total, 2),
                'cart_subtotal' => number_format($cart->subtotal, 2),
                'cart_discount' => number_format($cart->discount_total, 2),
                'cart_tax' => number_format($cart->tax_total, 2),
                'cart_total' => number_format($cart->grand_total, 2),
                'message' => 'Cart updated successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Cart update error', [
                'item_id' => $itemId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'message' => 'Could not update cart item'
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function remove($itemId)
    {
        try {
            // Get cart
            $cart = $this->cartService->getCart(
                auth()->id(),
                session()->getId()
            );

            // Remove item
            $this->cartService->removeItem($itemId);

            // Refresh cart
            $cart->refresh();

            return response()->json([
                'status' => true,
                'cart_count' => $cart->items->count(),
                'cart_subtotal' => number_format($cart->subtotal, 2),
                'cart_discount' => number_format($cart->discount_total, 2),
                'cart_tax' => number_format($cart->tax_total, 2),
                'cart_total' => number_format($cart->grand_total, 2),
                'message' => 'Item removed from cart'
            ]);
        } catch (\Exception $e) {
            \Log::error('Cart remove error', [
                'item_id' => $itemId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'message' => 'Could not remove item from cart'
            ], 500);
        }
    }

    /**
     * Get mini cart HTML
     */
    public function mini()
    {
        try {
            $cart = $this->cartService->getCart(
                auth()->id(),
                session()->getId()
            );

            return response()->json([
                'status' => true,
                'html' => view('components.frontend.mini-cart', compact('cart'))->render(),
                'cart_count' => $cart->items->count(),
                'cart_total' => number_format($cart->grand_total, 2),
                'message' => 'Mini cart loaded successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Mini cart error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'message' => 'Could not load mini cart'
            ], 500);
        }
    }


    public function applyCoupon(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:50',
            ]);

            // Get cart
            $cart = $this->cartService->getCart(
                auth()->id(),
                session()->getId()
            );

            // Apply coupon
            $result = $this->cartService->applyCoupon($request->code);

            if (!$result['success']) {
                return response()->json([
                    'status' => false,
                    'message' => $result['message']
                ], 422);
            }

            // Refresh cart to get updated values
            $cart->refresh();
            $cart->load(['coupons', 'items.product']);

            return response()->json([
                'status' => true,
                'message' => $result['message'],
                'coupon_code' => strtoupper($request->code),

                // Return both formatted (for display) and raw (for calculations)
                'discount' => number_format($result['discount'], 2),
                'discount_raw' => (float) $result['discount'],

                'cart_discount' => number_format($cart->discount_total, 2),
                'cart_discount_raw' => (float) $cart->discount_total,

                'cart_subtotal' => number_format($cart->subtotal, 2),
                'cart_subtotal_raw' => (float) $cart->subtotal,

                'cart_tax' => number_format($cart->tax_total, 2),
                'cart_tax_raw' => (float) $cart->tax_total,

                'cart_shipping' => number_format($cart->shipping_total, 2),
                'cart_shipping_raw' => (float) $cart->shipping_total,

                'cart_total' => number_format($cart->grand_total, 2),
                'cart_total_raw' => (float) $cart->grand_total,

                'coupon_id' => $result['coupon_id'] ?? null,
                'free_items' => $result['free_items'] ?? []
            ]);
        } catch (\Exception $e) {
            \Log::error('Apply coupon error', [
                'code' => $request->code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'message' => 'Could not apply coupon'
            ], 500);
        }
    }

    public function removeCoupon($couponId)
    {
        try {
            // Get cart
            $cart = $this->cartService->getCart(
                auth()->id(),
                session()->getId()
            );

            // Remove coupon
            $this->cartService->removeCoupon($couponId);

            // Refresh cart to get updated values
            $cart->refresh();
            $cart->load(['coupons', 'items.product']);

            return response()->json([
                'status' => true,
                'message' => 'Coupon removed successfully',

                // Return both formatted and raw values
                'cart_discount' => number_format($cart->discount_total, 2),
                'cart_discount_raw' => (float) $cart->discount_total,

                'cart_subtotal' => number_format($cart->subtotal, 2),
                'cart_subtotal_raw' => (float) $cart->subtotal,

                'cart_tax' => number_format($cart->tax_total, 2),
                'cart_tax_raw' => (float) $cart->tax_total,

                'cart_shipping' => number_format($cart->shipping_total, 2),
                'cart_shipping_raw' => (float) $cart->shipping_total,

                'cart_total' => number_format($cart->grand_total, 2),
                'cart_total_raw' => (float) $cart->grand_total,

                'has_coupons' => $cart->coupons->count() > 0
            ]);
        } catch (\Exception $e) {
            \Log::error('Remove coupon error', [
                'coupon_id' => $couponId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'message' => 'Could not remove coupon'
            ], 500);
        }
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        try {
            // Get cart
            $cart = $this->cartService->getCart(
                auth()->id(),
                session()->getId()
            );

            // Clear cart
            $this->cartService->clear();

            return response()->json([
                'status' => true,
                'message' => 'Cart cleared successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Clear cart error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'message' => 'Could not clear cart'
            ], 500);
        }
    }

    /**
     * Get cart count
     */
    public function count()
    {
        try {
            $cart = $this->cartService->getCart(
                auth()->id(),
                session()->getId()
            );

            return response()->json([
                'status' => true,
                'cart_count' => $cart->items->count(),
                'cart_total' => number_format($cart->grand_total, 2)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'message' => 'Could not get cart count'
            ], 500);
        }
    }
}