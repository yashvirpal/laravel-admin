<?php

namespace App\View\Components\Frontend;

use Illuminate\View\Component;

class AddToCart extends Component
{
    public $cartQty;
    public $product;
    public $qty;
    public $isSingle;

    public function __construct(
        $cartQty = 0,
        $product,
        $qty = 1,
        $isSingle = false
    ) {
        $this->cartQty = $cartQty;
        $this->product = $product;
        $this->qty = $qty;
        $this->isSingle = $isSingle;
    }

    public function render()
    {
        return view('components.frontend.add-to-cart');
    }
}


