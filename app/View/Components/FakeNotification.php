<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class FakeNotification extends Component
{
    public $cities;
    public $products;

    public function __construct($cities = [], $products = null)
    {
        $this->cities = $cities;

        $this->products = $products ?: Cache::remember('fake_notifications_products', 60, function () {
            return Product::active()
                ->inRandomOrder()
                ->take(10)
                ->get()
                ->map(function ($product) {
                    return [
                        'title' => $product->title,
                        'slug' => route('products.details', $product->slug),
                        'image' => $product->image ? $product->image_url : asset('frontend/images/product.webp'),
                        'price' => $product->finalPrice(),
                    ];
                })
                ->toArray();
        });
    }

    public function render(): View
    {
        return view('components.fake-notification');
    }
}