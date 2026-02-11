<?php
namespace App\Http\Controllers;

use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;

use App\Facades\CartServiceFacade as CartFacade;


use App\Http\Requests\Auth\LoginRequest;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cart
    ) {
    }

    public function login(LoginRequest $request)
    {
        try {
            $request->authenticate(); // handles email + password
            $request->session()->regenerate();
            CartFacade::mergeGuestCartIntoUserCart();
            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Login successful.',
                    //  'redirect_url' => route('dashboard'),
                ], 200);
            }

            return redirect()->intended(route('dashboard', absolute: false));

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

    public function checkOut(Request $request): JsonResponse|RedirectResponse
    {
        try {

            DB::beginTransaction();

            // 1️⃣ Create / Get User
            $user = $this->createUser($request);

            // 2️⃣ Create Billing Address (ALWAYS)
            $billingAddress = $this->createBillingAddress($request, $user->id);

            // 3️⃣ Create Shipping Address (CONDITIONAL)
            if ($request->boolean('differentShipping')) {
                $shippingAddress = $this->createShippingAddress($request, $user->id);
            } else {
                // Clone billing → shipping
                $shippingAddress = $billingAddress->replicate();
                $shippingAddress->type = 'shipping';
                $shippingAddress->save();
            }

            // 4️⃣ Create Order
            $order = $this->createOrder($user, $billingAddress, $shippingAddress);

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect_url' => route('order.success', $order->id)
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

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
        CartFacade::mergeGuestCartIntoUserCart();

        return $user;
    }

    private function createBillingAddress(Request $request, int $userId)
    {
        $data = $request->validate([
            'billing_first_name' => 'required|string',
            'billing_last_name' => 'required|string',
            'billing_address_line1' => 'required|string',
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
            'city' => $data['billing_city'],
            'state' => $data['billing_state'],
            'zip' => $data['billing_zip'],
            'phone' => $data['billing_phone'],
        ]);
    }

    private function createShippingAddress(Request $request, int $userId)
    {
        $data = $request->validate([
            'shipping_first_name' => 'required|string',
            'shipping_last_name' => 'required|string',
            'shipping_address_line1' => 'required|string',
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
            'city' => $data['shipping_city'],
            'state' => $data['shipping_state'],
            'zip' => $data['shipping_zip'],
            'phone' => $data['shipping_phone'],
        ]);
    }

    private function createOrder($user, $billing, $shipping)
    {
        return Order::create([
            'user_id' => $user->id,
            'billing_address_id' => $billing->id,
            'shipping_address_id' => $shipping->id,
            'status' => 'pending',
            'notes' => request('order_notes'),
            'total' => $this->cart->total(),
        ]);
    }





}

