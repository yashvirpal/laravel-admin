<?php
// database/seeders/OrderSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
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
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();
        $coupons = Coupon::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('ℹ️ No users or products found, skipping order seeding.');
            return;
        }

        // Create 20 orders
        for ($i = 1; $i <= 20; $i++) {
            $user = $users->random();

            // Get or create addresses for user
            $billingAddress = $this->getOrCreateAddress($user, 'billing');
            $shippingAddress = $this->getOrCreateAddress($user, 'shipping');

            // Generate order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            // Random status
            $statuses = ['pending', 'processing', 'completed', 'cancelled', 'refunded'];
            $status = $statuses[array_rand($statuses)];

            // Random payment method
            $paymentMethods = ['cash', 'card', 'upi', 'wallet', 'cod'];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

            // Random payment status
            $paymentStatus = $status === 'completed' ? 'paid' : ($status === 'cancelled' ? 'failed' : 'pending');

            // Random shipping method
            $shippingMethods = ['standard', 'express', 'overnight'];
            $shippingMethod = $shippingMethods[array_rand($shippingMethods)];
            $shippingCost = match ($shippingMethod) {
                'standard' => 0,
                'express' => 50,
                'overnight' => 100,
            };

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone ?? '9876543210',
                'billing_address_id' => $billingAddress->id,
                'shipping_address_id' => $shippingAddress->id,
                'billing_address' => $billingAddress->full_address,
                'shipping_address' => $shippingAddress->full_address,
                'subtotal' => 0, // Will update later
                'discount_total' => 0,
                'tax_total' => 0,
                'shipping_total' => $shippingCost,
                'total' => 0,
                'shipping_method' => $shippingMethod,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'status' => $status,
                'notes' => rand(0, 1) ? 'Please deliver before 6 PM' : null,
                'created_at' => Carbon::now()->subDays(rand(0, 90)),
            ]);

            $this->command->info("✅ Order created: {$order->order_number}");

            // Add 1-5 random products as order items
            $selectedProducts = $products->random(rand(1, 5));
            $subtotal = 0;

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);

                // Check if product has variants
                $variant = null;
                if ($product->has_variants) {
                    $variant = $product->variants()->inRandomOrder()->first();
                }

                // Get price
                if ($variant) {
                    $price = $variant->sale_price ?? $variant->regular_price ?? $variant->price ?? 0;
                    $productName = $product->title;
                    $variantName = $variant->name;
                    $sku = $variant->sku;
                } else {
                    $price = $product->sale_price ?? $product->regular_price ?? $product->price ?? 0;
                    $productName = $product->title;
                    $variantName = null;
                    $sku = $product->sku ?? null;
                }

                $itemSubtotal = $price * $quantity;
                $subtotal += $itemSubtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'product_name' => $productName,
                    'variant_name' => $variantName,
                    'sku' => $sku,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $itemSubtotal,
                ]);
            }

            // Randomly apply 0-2 coupons
            $discountTotal = 0;
            if ($coupons->isNotEmpty() && rand(0, 1)) {
                $numCoupons = rand(1, min(2, $coupons->count()));
                $selectedCoupons = $coupons->random($numCoupons);

                foreach ($selectedCoupons as $coupon) {
                    // Calculate discount (simplified)
                    $discount = 0;

                    // Get first action of coupon
                    $action = $coupon->actions->first();

                    if ($action) {
                        switch ($action->action) {
                            case 'fixed_discount':
                                $discount = min($action->value, $subtotal);
                                break;
                            case 'percentage_discount':
                                $discount = ($subtotal * $action->value) / 100;
                                break;
                            case 'discount_product':
                                $discount = rand(50, 200); // Random for seeding
                                break;
                        }
                    }

                    $discountTotal += $discount;

                    OrderCoupon::create([
                        'order_id' => $order->id,
                        'coupon_id' => $coupon->id,
                        'code' => $coupon->code,
                        'discount_amount' => $discount,
                    ]);
                }
            }

            // Ensure discount doesn't exceed subtotal
            $discountTotal = min($discountTotal, $subtotal);

            // Calculate tax (10% on subtotal after discount)
            $taxableAmount = $subtotal - $discountTotal;
            $taxTotal = round($taxableAmount * 0.10, 2);

            // Calculate final total
            $total = $subtotal - $discountTotal + $taxTotal + $shippingCost;

            // Update order totals
            $order->update([
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'tax_total' => $taxTotal,
                'total' => $total,
            ]);

            // Create transaction for completed/processing orders
            if (in_array($status, ['completed', 'processing'])) {
                $transactionStatus = $status === 'completed' ? 'success' : 'pending';

                Transaction::create([
                    'order_id' => $order->id,
                    'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
                    'amount' => $total,
                    'payment_method' => $paymentMethod,
                    'status' => $transactionStatus,
                    'response_data' => [
                        'gateway' => $paymentMethod,
                        'timestamp' => now()->toDateTimeString(),
                        'reference' => 'REF-' . strtoupper(Str::random(8)),
                    ],
                    'created_at' => $order->created_at,
                ]);
            }

            $this->command->info("   └─ Items: {$order->items->count()}, Coupons: {$order->coupons->count()}, Total: ₹{$total}");
        }

        $this->command->info("\n✅ 20 orders with items, coupons, and transactions created");
        $this->command->info("📊 Order Summary:");
        $this->command->info("   - Pending: " . Order::where('status', 'pending')->count());
        $this->command->info("   - Processing: " . Order::where('status', 'processing')->count());
        $this->command->info("   - Completed: " . Order::where('status', 'completed')->count());
        $this->command->info("   - Cancelled: " . Order::where('status', 'cancelled')->count());
    }

    /**
     * Get or create address for user
     */
    private function getOrCreateAddress(User $user, string $type): Address
    {
        // Try to get existing address
        $address = $user->addresses()->where('type', $type)->first();

        if ($address) {
            return $address;
        }

        // Create new address
        $faker = \Faker\Factory::create('en_IN'); // Indian locale

        return Address::create([
            'user_id' => $user->id,
            'type' => $type,
            'first_name' => explode(' ', $user->name)[0] ?? 'John',
            'last_name' => explode(' ', $user->name)[1] ?? 'Doe',
            'company' => rand(0, 1) ? $faker->company : null,
            'address_line1' => $faker->streetAddress,
            'address_line2' => rand(0, 1) ? $faker->secondaryAddress : null,
            'phone' => $user->phone ?? $faker->phoneNumber,
            'city' => $faker->city,
            'state' => $faker->state,
            'country' => 'India',
            'zip' => $faker->postcode,
            'is_default' => true,
            'status' => true,
        ]);
    }
}