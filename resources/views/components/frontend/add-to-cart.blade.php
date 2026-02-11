{{-- resources/views/components/frontend/add-to-cart.blade.php --}}

@php
    $inCart = $cartQty > 0;
@endphp

{{-- SINGLE PRODUCT PAGE --}}
@if($isSingle)
    @if($product->has_variants)
        {{-- For variant products, show disabled buttons until variant is selected --}}
        <button type="button" id="addCart{{ $product->id }}" class="add-to-cart-btn"
            onclick="addVariantToCart({{ $product->id }})" disabled>
            Select Options
        </button>

        <button type="button" id="buyNow{{ $product->id }}" class="add-to-cart-btn"
            onclick="addVariantToCart({{ $product->id }}, true)" disabled>
            Buy it now
        </button>
    @else
        {{-- For simple products --}}
        <button type="button" id="addCart{{ $product->id }}" class="add-to-cart-btn addCart{{ $product->id }}"
            onclick="addToCart({{ $product->id }}, 1, false, null)" {{ $inCart ? 'disabled' : '' }}>
            {{ $inCart ? 'Already in Cart' : 'Add to Cart' }}
        </button>

        <button type="button" id="buyNow{{ $product->id }}" class="add-to-cart-btn"
            onclick="addToCart({{ $product->id }}, 1, true, null)" {{ $inCart ? 'disabled' : '' }}>
            Buy it now
        </button>
    @endif

    {{-- SHOP / LISTING PAGE --}}
@else

    @if ($product->has_variants)

        <button type="button" title="Choose Options">
            <a href="{{ route('products.details', $product->slug) }}">
                <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                    <g id="Options_Icon" fill="#222">
                        <path d="M32 6L4 20l28 14 28-14L32 6z" />
                        <path d="M4 32l28 14 28-14v6L32 52 4 38v-6z" />
                        <path d="M4 44l28 14 28-14v6L32 64 4 50v-6z" />
                    </g>
                </svg>
            </a>
        </button>

    @else
        {{-- Simple product: Add to cart directly --}}
        <button class="btn-cart {{ $inCart ? 'in-cart' : '' }} addCart{{ $product->id }}" type="button"
            id="addCart{{ $product->id }}" onclick="addToCartFromListing({{ $product->id }})" {{ $inCart ? 'disabled' : '' }}
            title="{{ $inCart ? 'Already in Cart' : 'Add to Cart' }}">
            <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                <g id="Shopping_Bag" fill="{{ $inCart ? '#999' : '#222' }}" data-name="Shopping Bag">
                    <path
                        d="m32 54.685h-14.443a7.5 7.5 0 0 1 -7.453-8.34l2.821-30.066a1.5 1.5 0 0 1 1.494-1.36h17.581a1.5 1.5 0 0 1 0 3h-16.215l-2.7 28.734a4.5 4.5 0 0 0 4.469 5.032h14.446a1.5 1.5 0 0 1 0 3z" />
                    <path
                        d="m14.419 17.919a1.5 1.5 0 0 1 -1.07-2.551l5.5-5.6a1.5 1.5 0 0 1 1.07-.45h12.081a1.5 1.5 0 0 1 0 3h-11.449l-5.062 5.152a1.5 1.5 0 0 1 -1.07.449z" />
                    <path
                        d="m46.443 54.685h-14.443a1.5 1.5 0 0 1 0-3h14.443a4.5 4.5 0 0 0 4.472-5l-2.7-28.762h-16.215a1.5 1.5 0 0 1 0-3h17.581a1.5 1.5 0 0 1 1.494 1.36l2.825 30.09a7.5 7.5 0 0 1 -7.456 8.312z" />
                    <path
                        d="m49.581 17.919a1.5 1.5 0 0 1 -1.07-.449l-5.062-5.155h-11.449a1.5 1.5 0 0 1 0-3h12.078a1.5 1.5 0 0 1 1.07.45l5.5 5.6a1.5 1.5 0 0 1 -1.07 2.551z" />
                    <path
                        d="m32 30.835a8.157 8.157 0 0 1 -8.148-8.148v-1.5a1.5 1.5 0 0 1 3 0v1.5a5.148 5.148 0 0 0 10.3 0v-1.5a1.5 1.5 0 0 1 3 0v1.5a8.157 8.157 0 0 1 -8.152 8.148z" />
                </g>
            </svg>
        </button>
    @endif
@endif