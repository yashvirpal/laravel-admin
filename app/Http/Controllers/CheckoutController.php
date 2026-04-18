<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Address;
use App\Services\CartService;
use App\Services\PhonePeService;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    // ─────────────────────────────────────────────
    // 📄 CHECKOUT PAGE
    // ─────────────────────────────────────────────

    public function index()
    {
        $cart = $this->cartService->getCart(
            auth()->id(),
            session()->getId()
        );

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        $cart->load([
            'coupons' => fn($q) => $q->withPivot('discount_amount')
        ]);

        return view('frontend.checkout', compact('cart'));
    }

    // ─────────────────────────────────────────────
    // 🚚 UPDATE SHIPPING
    // ─────────────────────────────────────────────

    public function updateShipping(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'shipping_method' => 'required|string',
                'shipping_label' => 'required|string',
                'shipping_cost' => 'required|numeric|min:0',
            ]);

            $cart = $this->cartService->getCart(
                auth()->id(),
                session()->getId()
            );

            $this->cartService->setShipping(
                $request->shipping_method,
                $request->shipping_label,
                $request->shipping_cost
            );

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
            Log::error('Shipping update failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to update shipping method',
            ], 500);
        }
    }

    // ─────────────────────────────────────────────
    // 🔐 LOGIN FROM CHECKOUT PAGE
    // ─────────────────────────────────────────────

    public function login(LoginRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $oldSessionId = session()->getId();

            $request->authenticate();
            $userId = auth()->id();

            try {
                $this->cartService->mergeGuestCart($oldSessionId, $userId);
                Log::info('✅ Cart merged on checkout login');
            } catch (\Exception $e) {
                Log::error('❌ Cart merge failed on checkout login', ['error' => $e->getMessage()]);
            }

            $request->session()->regenerate();

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Login successful.',
                    'redirect_url' => route('page', 'checkout'),
                ]);
            }
            return redirect()->route('page', 'checkout');

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

    // ─────────────────────────────────────────────
    // ✅ PROCESS CHECKOUT
    // ─────────────────────────────────────────────

    public function checkOut(Request $request, PhonePeService $phonePe): JsonResponse
    {
        try {
            DB::beginTransaction();

            $cart = $this->cartService->getCart(
                auth()->id(),
                session()->getId()
            );

            if ($cart->items->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            $request->validate([
                'payment' => 'required|string',
                'shipping' => 'required|string',
            ]);

            // 1️⃣ Create / Get User
            $user = $this->createUser($request);

            // 2️⃣ Billing Address — create or update if ID passed
            $billingAddress = $this->createOrUpdateBillingAddress($request, $user->id);

            // 3️⃣ Shipping Address
            if ($request->boolean('differentShipping')) {
                // Create or update if ID passed
                $shippingAddress = $this->createOrUpdateShippingAddress($request, $user->id);
            } else {
                // Clone billing → shipping
                $shippingAddress = $billingAddress->replicate();
                $shippingAddress->type = 'shipping';
                $shippingAddress->save();
            }

            // dd($shippingAddress, $billingAddress, Address::where('user_id', auth()->id())->get()->toArray());

            // 4️⃣ Create Order
            $order = $this->createOrderFromCart($user, $billingAddress, $shippingAddress, $cart, $request);

            // 5️⃣ Mark coupons used & clear cart
            $this->cartService->markCouponsUsed();
            $this->cartService->clear();

            DB::commit();

            // 6️⃣ Route by payment method
            if ($request->payment === 'phonepe') {
                return $this->initiatePhonePePayment($phonePe, $order);
            }

            // COD / offline
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'redirect_url' => route('page', [
                    'slug' => 'order',
                    'order' => encrypt($order->id),
                    'success' => 1,
                ]),
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
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    // ─────────────────────────────────────────────
    // 💳 INITIATE PHONEPE PAYMENT
    // ─────────────────────────────────────────────

    private function initiatePhonePePayment(PhonePeService $phonePe, Order $order): JsonResponse
    {
        $response = $phonePe->createOrder(
            amount: $order->total,
            userId: $order->user_id,
            redirectUrl: route('payment.callback', ['order' => encrypt($order->id)]),
            callbackUrl: route('payment.webhook')
        );

        $redirectUrl = $response['redirect_url'] ?? null;

        if (!$response['success'] || !$redirectUrl) {
            Log::error('PhonePe payment initiation failed', [
                'order_id' => $order->id,
                'response' => $response,
            ]);

            $order->update(['payment_status' => 'failed']);

            return response()->json([
                'success' => false,
                'message' => 'Payment gateway error. Please try again.',
            ], 500);
        }

        // ✅ Use DB::transaction to guarantee the save
        DB::transaction(function () use ($order, $response) {
            $updated = DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'transaction_id' => $response['transaction_id'],
                    'payment_status' => 'initiated',
                    'updated_at' => now(),
                ]);

            Log::info('PhonePe transaction_id saved', [
                'order_id' => $order->id,
                'transaction_id' => $response['transaction_id'],
                'rows_updated' => $updated,
            ]);
        });

        // ✅ Verify it actually persisted
        $saved = DB::table('orders')->where('id', $order->id)->value('transaction_id');

        if (empty($saved)) {
            Log::error('PhonePe: transaction_id failed to persist', ['order_id' => $order->id]);

            return response()->json([
                'success' => false,
                'message' => 'Order update failed. Please try again.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'redirect_url' => $redirectUrl,
        ]);
    }
    // ─────────────────────────────────────────────
    // 🔁 PHONEPE REDIRECT CALLBACK
    // ─────────────────────────────────────────────

    public function paymentCallback(Request $request, PhonePeService $phonePe): RedirectResponse
    {
        try {
            $orderId = decrypt($request->query('order'));
            $order = Order::findOrFail($orderId);

            // ✅ Re-fetch fresh from DB — don't trust cached model
            $transactionId = DB::table('orders')
                ->where('id', $order->id)
                ->value('transaction_id');

            Log::info('PhonePe callback hit', [
                'order_id' => $order->id,
                'transaction_id' => $transactionId,
                'payment_status' => $order->payment_status,
            ]);

            if (empty($transactionId)) {
                Log::warning('PhonePe callback: transaction_id is null', [
                    'order_id' => $order->id,
                ]);

                $order->update(['payment_status' => 'failed']);
                // ✅ Record the failed attempt even without a transaction ID
                // ✅ On missing transaction_id in paymentCallback:

                $this->recordTransaction($order, null, 'failed', ['error' => 'transaction_id missing at callback']);


                return redirect()->route('page', ['slug' => 'order', 'order' => encrypt($order->id), 'failed' => 1,])->with('error', 'Payment could not be verified. Please try again.');
            }

            // ✅ Verify with PhonePe
            $status = $phonePe->checkStatus($transactionId);
            $state = $status['state'] ?? 'FAILED';

            Log::info('PhonePe order status', [
                'order_id' => $order->id,
                'transaction_id' => $transactionId,
                'state' => $state,
            ]);

            if ($state === 'COMPLETED') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                ]);
                // ✅ On success in paymentCallback:
                $this->recordTransaction($order, $transactionId, 'success', $status);

                return redirect()->route('page', [
                    'slug' => 'order',
                    'order' => encrypt($order->id),
                    'success' => 1
                ]);
            }

            $order->update(['payment_status' => 'failed']);
            // ✅ On failed state in paymentCallback:
            $this->recordTransaction($order, $transactionId, 'failed', $status);


            return redirect()->route('page', [
                'slug' => 'order',
                'order' => encrypt($order->id),
                'failed' => 1
            ]);

        } catch (\Exception $e) {
            Log::error('PhonePe callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // ✅ On exception in paymentCallback (guard still applies):
            if (isset($order)) {
                if (isset($order)) {
                    $this->recordTransaction($order, null, 'failed', ['error' => $e->getMessage()]);
                }

                return redirect()->route('page', [
                    'slug' => 'order',
                    'order' => encrypt($order->id),
                    'failed' => 1,
                ]);
            }

            return redirect()->route('home')->with('error', 'Something went wrong. Please contact support.');

            // return redirect()->route('page', [
            //     'slug' => 'order',
            //     'order' => encrypt($order->id),
            //     'failed' => 1
            // ]);
        }
    }


    // ─────────────────────────────────────────────
    // 🔄 RETRY PAYMENT
    // ─────────────────────────────────────────────

    public function retryPayment(Request $request, PhonePeService $phonePe): JsonResponse
    {
        try {
            $orderId = decrypt($request->input('order'));
            $order = Order::findOrFail($orderId);

            // ✅ Only allow retry for orders belonging to the authenticated user
            if ($order->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.',
                ], 403);
            }

            // ✅ Only allow retry if payment actually failed or was never completed
            if (!in_array($order->payment_status, ['pending', 'failed', 'initiated'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order cannot be retried.',
                ], 422);
            }

            // ✅ Reset payment status before retrying
            $order->update([
                'payment_status' => 'pending',
                'transaction_id' => null,
            ]);

            return $this->initiatePhonePePayment($phonePe, $order);

        } catch (\Throwable $e) {
            Log::error('Retry payment failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    // ─────────────────────────────────────────────
    // 💸 REFUND
    // ─────────────────────────────────────────────

    public function refund(PhonePeService $phonePe, Order $order): JsonResponse
    {
        if (!$order->transaction_id) {
            return response()->json([
                'success' => false,
                'message' => 'No transaction ID found for this order.',
            ], 400);
        }

        $response = $phonePe->refund($order->transaction_id, $order->total);

        if ($response['success']) {
            $order->update(['payment_status' => 'refunded']);
        }

        return response()->json([
            'success' => $response['success'],
            'data' => $response['data'],
        ]);
    }

    // ─────────────────────────────────────────────
    // 🔍 VERIFY STATUS
    // ─────────────────────────────────────────────

    public function verify(PhonePeService $phonePe, Order $order): JsonResponse
    {
        if (!$order->transaction_id) {
            return response()->json([
                'success' => false,
                'message' => 'No transaction ID found.',
            ], 400);
        }

        $status = $phonePe->checkStatus($order->transaction_id);

        return response()->json([
            'success' => true,
            'data' => $status,
        ]);
    }

    // ─────────────────────────────────────────────
    // 🔒 PRIVATE HELPERS
    // ─────────────────────────────────────────────

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
            'name' => trim($request->billing_first_name . ' ' . $request->billing_last_name),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'country_code' => $request->country_code ?? '91',
            'password' => Hash::make($validated['password']),
            'status' => 1,
        ]);

        event(new Registered($user));
        Auth::login($user);

        try {
            $this->cartService->mergeGuestCart(session()->getId(), $user->id);
        } catch (\Exception $e) {
            Log::error('Cart merge failed for new user', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $user;
    }

    /**
     * Create billing address or update existing if billing_address_id passed
     */
    private function createOrUpdateBillingAddress(Request $request, int $userId): Address
    {
        $data = $request->validate([
            'billing_address_id' => 'nullable|exists:addresses,id,user_id,' . $userId,
            'billing_first_name' => 'required|string',
            'billing_last_name' => 'required|string',
            'billing_address_line1' => 'required|string',
            'billing_address_line2' => 'nullable|string',
            'billing_city' => 'required|string',
            'billing_state' => 'required|string',
            'billing_zip' => 'required|string',
            'billing_phone' => 'required|string',
        ]);

        $fields = [
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
        ];

        // ✅ Update if ID passed and belongs to this user
        if (!empty($data['billing_address_id'])) {
            $address = Address::where('id', $data['billing_address_id'])
                ->where('user_id', $userId)
                ->first();

            if ($address) {
                $address->update($fields);
                $address->makeDefault();
                return $address->fresh();
            }
        }

        $address = Address::create($fields);
        $address->makeDefault();
        return $address->fresh();
    }

    /**
     * Create shipping address or update existing if shipping_address_id passed
     */
    private function createOrUpdateShippingAddress(Request $request, int $userId): Address
    {
        $data = $request->validate([
            'shipping_address_id' => 'nullable|exists:addresses,id,user_id,' . $userId,
            'shipping_first_name' => 'required|string',
            'shipping_last_name' => 'required|string',
            'shipping_address_line1' => 'required|string',
            'shipping_address_line2' => 'nullable|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'required|string',
            'shipping_zip' => 'required|string',
            'shipping_phone' => 'required|string',
        ]);

        $fields = [
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
        ];

        // ✅ Update if ID passed and belongs to this user
        if (!empty($data['shipping_address_id'])) {
            $address = Address::where('id', $data['shipping_address_id'])
                ->where('user_id', $userId)
                ->first();

            if ($address) {
                $address->update($fields);
                $address->makeDefault();
                return $address->fresh();
            }
        }

        $address = Address::create($fields);
        $address->makeDefault();
        return $address->fresh();
    }

    private function createOrderFromCart($user, $billing, $shipping, $cart, $request): Order
    {
        $order = Order::create([
            'order_number' => 'TEMP-' . Str::random(8), // temp — updated below
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone,
            'billing_address_id' => $billing->id,
            'shipping_address_id' => $shipping->id,
            'billing_address' => $billing->full_address,
            'shipping_address' => $shipping->full_address,
            'status' => 'pending',
            'payment_method' => $request->payment,
            'payment_status' => 'pending',
            'transaction_id' => null,
            'notes' => $request->order_notes,
            'subtotal' => $cart->subtotal,
            'discount_total' => $cart->discount_total,
            'tax_total' => $cart->tax_total,
            'shipping_total' => $cart->shipping_total,
            'shipping_method' => $cart->shipping_method,
            'total' => $cart->grand_total,
        ]);

        $order->update([
            'order_number' => generateOrderNumber($order),
        ]);

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
                'custom_data' => (array) $item->custom_data,
            ]);
        }

        foreach ($cart->coupons as $coupon) {
            $order->coupons()->create([
                'coupon_id' => $coupon->id,
                'code' => $coupon->code,
                'discount_amount' => $coupon->pivot->discount_amount,
            ]);
        }

        return $order;
    }


    /**
     * Record a transaction entry regardless of success or failure.
     * Handles schema constraints: non-null transaction_id, valid enum, text response_data.
     */
    private function recordTransaction(Order $order, ?string $transactionId, string $status, array $responseData = []): void
    {
        $type = strtolower(
            $responseData['paymentInstrument']['type']
            ?? $responseData['data']['paymentInstrument']['type']
            ?? ''
        );

        $paymentMethod = match (true) {
            str_contains($type, 'card') => 'card',
            str_contains($type, 'upi') => 'upi',
            str_contains($type, 'wallet') => 'wallet',
            str_contains($type, 'net_banking'),
            str_contains($type, 'netbanking') => 'cash',
            default => 'card',
        };

        $order->transactions()->create([
            'transaction_id' => $transactionId ?? 'FAILED-' . $order->id . '-' . time(),
            'amount' => $order->total,
            'payment_method' => $paymentMethod,
            'status' => $status,
            'response_data' => json_encode($responseData),
        ]);
    }
}