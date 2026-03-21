<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderCoupon;
use App\Models\Transaction;
use App\Models\Address;
use App\Models\Coupon;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users    = User::all();
        $products = Product::with('variants')->get();
        $coupons  = Coupon::with('actions')->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('⚠️  No users or products found, skipping order seeding.');
            return;
        }

        for ($i = 1; $i <= 20; $i++) {
            $user = $users->random();

            $billingAddress  = $this->getOrCreateAddress($user, 'billing');
            $shippingAddress = $this->getOrCreateAddress($user, 'shipping');

            // Random status & payment
            $status        = collect(['pending', 'processing', 'completed', 'cancelled', 'refunded'])->random();
            $paymentMethod = collect(['cash', 'card', 'upi', 'wallet', 'cod'])->random();

            $paymentStatus = match ($status) {
                'completed'           => 'paid',
                'cancelled','refunded'=> 'failed',
                default               => 'pending',
            };

            $shippingMethod = collect(['standard', 'express', 'overnight'])->random();
            $shippingCost   = match ($shippingMethod) {
                'express'   => 50,
                'overnight' => 100,
                default     => 0,
            };

            // ✅ order_number can be empty string initially — NOT null (column is unique string)
            $order = Order::create([
                'order_number'        => 'TEMP-' . Str::random(8), // ✅ temp unique value
                'user_id'             => $user->id,
                'customer_name'       => $user->name,
                'customer_email'      => $user->email,
                'customer_phone'      => $user->phone ?? '9876543210',
                'billing_address_id'  => $billingAddress->id,
                'shipping_address_id' => $shippingAddress->id,
                'billing_address'     => $billingAddress->full_address,
                'shipping_address'    => $shippingAddress->full_address,
                'subtotal'            => 0,
                'discount_total'      => 0,
                'tax_total'           => 0,
                'shipping_total'      => $shippingCost,
                'total'               => 0,
                'shipping_method'     => $shippingMethod,
                'payment_method'      => $paymentMethod,
                'payment_status'      => $paymentStatus,
                'status'              => $status,
                'notes'               => rand(0, 1) ? 'Please deliver before 6 PM' : null,
                'created_at'          => Carbon::now()->subDays(rand(0, 90)),
                'updated_at'          => Carbon::now()->subDays(rand(0, 90)),
            ]);

            // ✅ Now update with real order number using ID
            $order->update([
                'order_number' =>generateOrderNumber($order),
            ]);

            // ── ORDER ITEMS ──────────────────────────────────────
            $count    = min(rand(1, 5), $products->count());
            $selected = $products->random($count);
            $subtotal = 0;

            foreach ($selected as $product) {
                $quantity = rand(1, 3);
                $variant  = null;

                if ($product->has_variants && $product->variants->isNotEmpty()) {
                    $variant = $product->variants->random();
                }

                $price = 0;

                if ($variant) {
                    // ✅ Check common price column names safely
                    $price = $variant->sale_price
                        ?? $variant->regular_price
                        ?? $variant->price
                        ?? 0;
                } else {
                    $price = $product->sale_price
                        ?? $product->regular_price
                        ?? $product->price
                        ?? 0;
                }

                $price        = (float) $price; // ✅ cast — avoid null arithmetic
                $itemSubtotal = $price * $quantity;
                $subtotal    += $itemSubtotal;

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $product->id,
                    'variant_id'   => $variant?->id,
                    'product_name' => $product->title,
                    'variant_name' => $variant?->name,
                    'sku'          => $variant?->sku ?? $product->sku ?? null,
                    'quantity'     => $quantity,
                    'price'        => $price,
                    'subtotal'     => $itemSubtotal,
                ]);
            }

            // ── COUPONS ──────────────────────────────────────────
            $discountTotal = 0;

            if ($coupons->isNotEmpty() && rand(0, 1)) {
                $numCoupons      = rand(1, min(2, $coupons->count()));
                $selectedCoupons = $coupons->random($numCoupons);

                // ✅ random() returns Collection or single model — normalize
                if (! $selectedCoupons instanceof \Illuminate\Support\Collection) {
                    $selectedCoupons = collect([$selectedCoupons]);
                }

                foreach ($selectedCoupons as $coupon) {
                    $action   = $coupon->actions->first();
                    $discount = 0;

                    if ($action) {
                        $discount = match ($action->action) {
                            'fixed_discount'      => min((float) $action->value, $subtotal),
                            'percentage_discount' => round(($subtotal * (float) $action->value) / 100, 2),
                            default               => round(rand(50, 200) / 1, 2),
                        };
                    }

                    $discountTotal += $discount;

                    OrderCoupon::create([
                        'order_id'        => $order->id,
                        'coupon_id'       => $coupon->id,
                        'code'            => $coupon->code,
                        'discount_amount' => $discount,
                    ]);
                }
            }

            // ── TOTALS ───────────────────────────────────────────
            $discountTotal = min($discountTotal, $subtotal);
            $taxableAmount = $subtotal - $discountTotal;
            $taxTotal      = round($taxableAmount * 0.10, 2);
            $total         = round($subtotal - $discountTotal + $taxTotal + $shippingCost, 2);

            $order->update([
                'subtotal'       => $subtotal,
                'discount_total' => $discountTotal,
                'tax_total'      => $taxTotal,
                'total'          => $total,
            ]);

            // ── TRANSACTION ──────────────────────────────────────
            if (in_array($status, ['completed', 'processing'])) {
                Transaction::create([
                    'order_id'       => $order->id,
                    'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
                    'amount'         => $total,
                    'payment_method' => $paymentMethod === 'phonepe' ? 'upi' : $paymentMethod, // ✅ match enum
                    'status'         => $status === 'completed' ? 'success' : 'pending',
                    'response_data'  => json_encode([  // ✅ cast to JSON string — column is text
                        'gateway'   => $paymentMethod,
                        'timestamp' => now()->toDateTimeString(),
                        'reference' => 'REF-' . strtoupper(Str::random(8)),
                    ]),
                    'created_at' => $order->created_at,
                    'updated_at' => $order->created_at,
                ]);
            }

            $itemCount   = $order->items()->count();
            $couponCount = $order->coupons()->count();
            $this->command->info("✅ {$order->order_number} | Items: {$itemCount} | Coupons: {$couponCount} | ₹{$total}");
        }

        $this->command->newLine();
        $this->command->info('📊 Order Summary:');
        $this->command->info('   Pending:    ' . Order::where('status', 'pending')->count());
        $this->command->info('   Processing: ' . Order::where('status', 'processing')->count());
        $this->command->info('   Completed:  ' . Order::where('status', 'completed')->count());
        $this->command->info('   Cancelled:  ' . Order::where('status', 'cancelled')->count());
        $this->command->info('   Refunded:   ' . Order::where('status', 'refunded')->count());
    }

    private function getOrCreateAddress(User $user, string $type): Address
    {
        $existing = $user->addresses()->where('type', $type)->first();

        if ($existing) {
            return $existing;
        }

        $faker = \Faker\Factory::create('en_IN');

        $nameParts = explode(' ', $user->name, 2);

        return Address::create([
            'user_id'       => $user->id,
            'type'          => $type,
            'first_name'    => $nameParts[0] ?? 'John',
            'last_name'     => $nameParts[1] ?? 'Doe',     // ✅ safer split
            'company'       => rand(0, 1) ? $faker->company : null,
            'address_line1' => $faker->streetAddress,
            'address_line2' => rand(0, 1) ? $faker->secondaryAddress : null,
            'phone'         => $user->phone ?? $faker->numerify('9#########'),
            'city'          => $faker->city,
            'state'         => $faker->state,
            'country'       => 'India',
            'zip'           => $faker->numerify('######'),  // ✅ Indian 6-digit pincode
            'is_default'    => true,
            'status'        => true,
        ]);
    }
}