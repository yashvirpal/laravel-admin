<?php
// app/Http/Controllers/CheckoutController.php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;
use App\Facades\CartServiceFacade as CartFacade;
use App\Http\Requests\Auth\LoginRequest;

class CheckoutController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display checkout page
     */
    public function index()
    {
        $cart = $this->cartService->getCart(
            auth()->id(),
            session()->getId()
        );

        // Ensure cart has items
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        // Load coupons with pivot data
        $cart->load([
            'coupons' => function ($query) {
                $query->withPivot('discount_amount');
            }
        ]);

        return view('frontend.checkout', compact('cart'));
    }

    /**
     * Update shipping method
     */
    public function updateShipping(Request $request)
    {
        try {
            $request->validate([
                'shipping_method' => 'required|string',
                'shipping_label' => 'required|string',
                'shipping_cost' => 'required|numeric|min:0'
            ]);

            Log::info('Updating shipping method', $request->all());

            // Get cart
            $cart = $this->cartService->getCart(
                auth()->id(),
                session()->getId()
            );

            // Update shipping
            $this->cartService->setShipping(
                $request->shipping_method,
                $request->shipping_label,
                $request->shipping_cost
            );

            // Refresh cart
            $cart->refresh();

            return response()->json([
                'status' => true,
                'message' => 'Shipping method updated',
                'cart_subtotal' => number_format($cart->subtotal, 2),
                'cart_subtotal_raw' => (float) $cart->subtotal,
                'cart_discount' => number_format($cart->discount_total, 2),
                'cart_discount_raw' => (float) $cart->discount_total,
                'cart_tax' => number_format($cart->tax_total, 2),
                'cart_tax_raw' => (float) $cart->tax_total,
                'cart_shipping' => number_format($cart->shipping_total, 2),
                'cart_shipping_raw' => (float) $cart->shipping_total,
                'cart_total' => number_format($cart->grand_total, 2),
                'cart_total_raw' => (float) $cart->grand_total,
            ]);

        } catch (\Exception $e) {
            Log::error('Shipping update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to update shipping method'
            ], 500);
        }
    }

    /**
     * Login from checkout page
     */
    public function login(LoginRequest $request)
    {
        try {
            // Save old session ID before authentication
            $oldSessionId = session()->getId();

            // Authenticate user
            $request->authenticate();
            $userId = auth()->id();

            Log::info('Checkout login - merging cart', [
                'user_id' => $userId,
                'session_id' => $oldSessionId
            ]);

            // Merge cart BEFORE session regeneration
            try {
                $this->cartService->mergeGuestCart($oldSessionId, $userId);
                Log::info('✅ Cart merged in checkout login');
            } catch (\Exception $e) {
                Log::error('❌ Cart merge failed in checkout login', [
                    'error' => $e->getMessage()
                ]);
            }

            // Now regenerate session
            $request->session()->regenerate();

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Login successful.',
                ], 200);
            }

            return redirect()->route('checkout.index');

        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'errors' => $e->errors(),
                ], 422);
            }

            throw $e;
        }
    }

    /**
     * Process checkout
     */
    public function checkOut(Request $request): JsonResponse|RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Get cart
            $cart = $this->cartService->getCart(
                auth()->id(),
                session()->getId()
            );

            if ($cart->items->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            // Validate payment and shipping
            $request->validate([
                'payment' => 'required|string',
                'shipping' => 'required|string',
            ]);

            // 1️⃣ Create / Get User
            $user = $this->createUser($request);

            // 2️⃣ Create Billing Address
            $billingAddress = $this->createBillingAddress($request, $user->id);

            // 3️⃣ Create Shipping Address
            if ($request->boolean('differentShipping')) {
                $shippingAddress = $this->createShippingAddress($request, $user->id);
            } else {
                // Clone billing → shipping
                $shippingAddress = $billingAddress->replicate();
                $shippingAddress->type = 'shipping';
                $shippingAddress->save();
            }

            // 4️⃣ Create Order from Cart
            $order = $this->createOrderFromCart($user, $billingAddress, $shippingAddress, $cart, $request);

            // 5️⃣ Mark coupons as used
            $this->cartService->markCouponsUsed();

            // 6️⃣ Clear cart
            $this->cartService->clear();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'redirect_url' => route('page', ['slug' => 'thank-you', 'order' => encrypt($order->id)]),
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Checkout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?? 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    /**
     * Create or get user
     */
    private function createUser(Request $request): User
    {
        if (Auth::check()) {
            return Auth::user();
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'unique:users,phone'],
            'password' => ['required', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->billing_first_name . ' ' . $request->billing_last_name,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'country_code' => $request->country_code ?? '91',
            'password' => Hash::make($validated['password']),
            'status' => 1,
        ]);

        event(new Registered($user));
        Auth::login($user);

        // Merge cart after creating new user
        try {
            $this->cartService->mergeGuestCart(session()->getId(), $user->id);
        } catch (\Exception $e) {
            Log::error('Failed to merge cart for new user', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }

        return $user;
    }

    /**
     * Create billing address
     */
    private function createBillingAddress(Request $request, int $userId)
    {
        $data = $request->validate([
            'billing_first_name' => 'required|string',
            'billing_last_name' => 'required|string',
            'billing_address_line1' => 'required|string',
            'billing_address_line2' => 'nullable|string',
            'billing_city' => 'required|string',
            'billing_state' => 'required|string',
            'billing_zip' => 'required|string',
            'billing_phone' => 'required|string',
        ]);

        return Address::create([
            'user_id' => $userId,
            'type' => 'billing',
            'first_name' => $data['billing_first_name'],
            'last_name' => $data['billing_last_name'],
            'address_line1' => $data['billing_address_line1'],
            'address_line2' => $data['billing_address_line2'] ?? null,
            'city' => $data['billing_city'],
            'state' => $data['billing_state'],
            'zip' => $data['billing_zip'],
            'phone' => $data['billing_phone'],
        ]);
    }
    /**
     * Create shipping address
     */
    private function createShippingAddress(Request $request, int $userId)
    {
        $data = $request->validate([
            'shipping_first_name' => 'required|string',
            'shipping_last_name' => 'required|string',
            'shipping_address_line1' => 'required|string',
            'shipping_address_line2' => 'nullable|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'required|string',
            'shipping_zip' => 'required|string',
            'shipping_phone' => 'required|string',
        ]);

        return Address::create([
            'user_id' => $userId,
            'type' => 'shipping',
            'first_name' => $data['shipping_first_name'],
            'last_name' => $data['shipping_last_name'],
            'address_line1' => $data['shipping_address_line1'],
            'address_line2' => $data['shipping_address_line2'] ?? null,
            'city' => $data['shipping_city'],
            'state' => $data['shipping_state'],
            'zip' => $data['shipping_zip'],
            'phone' => $data['shipping_phone'],
        ]);
    }


    /**
     * Create order from cart
     */
    private function createOrderFromCart($user, $billing, $shipping, $cart, $request)
    {
        // Generate order number
        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

        // Create order
        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone,
            'billing_address_id' => $billing->id,
            'shipping_address_id' => $shipping->id,

            // Store formatted addresses as backup
            'billing_address' => $billing->full_address,
            'shipping_address' => $shipping->full_address,

            'status' => 'pending',
            'payment_method' => $request->payment,
            'payment_status' => 'pending',
            'notes' => $request->order_notes,

            // Cart totals
            'subtotal' => $cart->subtotal,
            'discount_total' => $cart->discount_total,
            'tax_total' => $cart->tax_total,
            'shipping_total' => $cart->shipping_total,
            'shipping_method' => $cart->shipping_method,
            'total' => $cart->grand_total,
        ]);

        // Create order items
        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'product_name' => $item->product->title,
                'variant_name' => $item->variant?->name,
                'sku' => $item->variant?->sku ?? $item->product->sku ?? null,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'subtotal' => $item->price * $item->quantity,
            ]);
        }

        // Create order coupons
        foreach ($cart->coupons as $coupon) {
            $order->coupons()->create([
                'coupon_id' => $coupon->id,
                'code' => $coupon->code,
                'discount_amount' => $coupon->pivot->discount_amount,
            ]);
        }

        return $order;
    }
}