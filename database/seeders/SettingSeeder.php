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

        // $settings = [
        //     'site_name' => 'My Awesome Site',
        //     'site_email' => 'info@localhost.com',
        //     'currency' => 'INR',
        //     'currency_symbol' => '₹',
        //     'phone' => '+1 234 567 890',
        //     'address' => '123 Main Street, City, Country',
        //     'footer_text' => '© 2025 My Awesome Site. All rights reserved.',
        //     'payment_gateways' => json_encode($paymentGateways),
        //     'shipping_methods' => json_encode($shippingMethods),
        // ];
        $settings = [
            'site_name' => 'Jovial Vision',
            'site_email' => 'info@localhost.com',
            'admin_email' => 'contact@yashvirpal.com',
            'currency' => 'INR',
            'currency_symbol' => '₹',
            'phone' => '+91 99115 73173',
            'address' => 'Diksha Mehta, 23b/5 New Rohtak Road, Near Liberty Cinema, Dev Nagar Karol Bagh, New Delhi 110005 Opp. Bikaner.',
            'email' => 'jovialvision04@gmail.com',
            'map' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d28010.10624782622!2d77.17272198213867!3d28.651834452807428!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d029c5f402ed3%3A0x942174294c9dd946!2sKarol%20Bagh%2C%20New%20Delhi%2C%20Delhi!5e0!3m2!1sen!2sin!4v1776518039722!5m2!1sen!2sin',
            'footer_text' => '© 2025 My Awesome Site. All rights reserved.',
            'payment_gateways' => json_encode($paymentGateways),
            'shipping_methods' => json_encode($shippingMethods),
            'social' => json_encode([
                'facebook' => 'https://facebook.com/jovialvision4',
                'instagram' => 'https://instagram.com/jovialvision',
                'twitter' => 'https://x.com/jovialvision?s=21',
                'youtube' => 'https://youtube.com/@jovial_vision',
            ]),
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
