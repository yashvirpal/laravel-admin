<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\User;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::first(); // Assign first user as author

        // List of pages
        $pages = [
            'Home',
            'About Us',
            'Contact Us',
            'Bulk Enquiry',
            'Privacy Policy',
            'Terms & Conditions',
            'Refund & Returns',
            'Shipping Policy',
            'Sitemap',
            'Wishlist',
            'Cart',
            'Checkout',
            'Login',
            'Register',
            'Forgot Password',
            'Reset Password',
            'Shop',
            'Category'
        ];

        // Template map (from your config or define inline)
        $templateMap = [
            'home' => 'home',
            'about-us' => 'about',
            'contact-us' => 'contact',
            'bulk-enquiry' => 'default',
            'privacy-policy' => 'default',
            'terms-conditions' => 'default',
            'refund-returns' => 'default',
            'shipping-policy' => 'default',
            'sitemap' => 'default',
            'wishlist' => 'wishlist',
            'cart' => 'cart',
            'checkout' => 'checkout',
            'login' => 'auth',
            'register' => 'auth',
            'forgot-password' => 'auth',
            'reset-password' => 'auth',
            'shop' => 'shop',
            'category' => 'category',
        ];

        foreach ($pages as $title) {
            $slug = Str::slug($title);
            $template = $templateMap[$slug] ?? 'default';

            $page = Page::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'short_description' => 'Coming soon',
                    'description' => 'Coming soon',
                    'template' => $template,
                    'status' => true,
                    'author_id' => $author?->id,
                ]
            );

            $this->command->info("✅ Page created/updated: {$page->title} with template '{$template}'");
        }

    }
}
