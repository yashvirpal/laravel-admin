<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentGateways = [];

        foreach (config('settings.payment_gateways') as $gateway) {
            $paymentGateways[$gateway] = [
                'enabled' => in_array($gateway, ['phonepe']) ? '1' : '0',
                'description' => null,
                'merchant_id' => null,
                'secret_key' => null,
                'webhook_key' => null,
                'webhook_url' => null,
            ];
        }
        $shippingMethods = [];

        foreach (config('settings.shipping_methods') as $method) {
            $shippingMethods[$method] = [
                'enabled' => in_array($method, ['flat_rate', 'free_shipping']) ? '1' : '0',
                'amount' => in_array($method, ['free_shipping']) ? '0.00' : '50.0',
                'description' => null,
            ];
        }


        $settings = [
            'site_name' => 'My Awesome Site',
            'site_email' => 'info@localhost.com',
            'currency' => 'INR',
            'currency_symbol' => '₹',
            'phone' => '+1 234 567 890',
            'address' => '123 Main Street, City, Country',
            'footer_text' => '© 2025 My Awesome Site. All rights reserved.',
            'payment_gateways' => json_encode($paymentGateways),
            'shipping_methods' => json_encode($shippingMethods),
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->command->info('✅ Default settings seeded successfully.');
    }
}
