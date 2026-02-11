{{-- resources/views/components/frontend/mini-cart.blade.php --}}

@if($cart->items->isEmpty())
    <p class="text-center mt-2">Cart is empty</p>
@else
    @foreach($cart->items as $item)
        <div class="product" id="mini-cart-item-{{ $item->id }}">
            <div class="product-details">
                <h4 class="product-title">
                    <a href="{{ route('products.details', $item->product->slug) }}">
                        {{ $item->product->title }}
                    </a>
                </h4>
                @if($item->variant)
                    <small class="text-muted d-block">{{ $item->variant->name }}</small>
                @endif

                <span class="cart-product-info">
                    @if($item->price == 0)
                        <span class="badge bg-success">FREE</span>
                    @else
                        <span class="cart-product-qty">{{ $item->quantity }}</span>
                        × {{ currencyformat($item->price) }} = {{ currencyformat($item->line_total) }}
                    @endif
                </span>
            </div>

            <figure class="product-image-container">
                <a href="{{ route('products.details', $item->product->slug) }}" class="product-image">
                    <img src="{{ $item->product->image ? $item->product->image_url : asset('frontend/images/product.webp') }}" 
                         width="80" height="80" alt="{{ $item->product->title }}">
                </a>

                <a href="javascript:void(0)" 
                   onclick="removeFromCart({{ $item->id }})" 
                   class="btn-remove"
                   title="Remove Product">
                    <span>×</span>
                </a>
            </figure>
        </div>
    @endforeach

    <div class="cart-footer">
        <div class="subtotal">
            <span>Subtotal:</span>
            <strong>{{ currencyformat($cart->subtotal) }}</strong>
        </div>

        @if($cart->discount_total > 0)
            <div class="subtotal text-success">
                <span>Discount:</span>
                <strong>-{{ currencyformat($cart->discount_total) }}</strong>
            </div>
        @endif

        <div class="subtotal">
            <span>Total:</span>
            <strong>{{ currencyformat($cart->grand_total) }}</strong>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('page','cart') }}" class="btn btn-sm mybtn">View Cart</a>
            <a href="{{ route('page','checkout') }}" class="btn btn-sm btn-primary mybtn">Checkout</a>
        </div>
    </div>
@endif