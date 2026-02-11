<div class="product-price">

    @if ($item->isVariable())
        {{-- VARIABLE PRODUCT PRICE RANGE --}}
        @php
            $min = currencyformat($item->minVariantPrice());
            $max = currencyformat($item->maxVariantPrice());
        @endphp

        <span class="price-sale">{{ $min }} @if($min != $max) — {{ $max }} @endif</span>

    @else
        {{-- SIMPLE PRODUCT PRICING --}}
        @if ($item->sale_price)
            <span class="price-sale">
                {{ currencyformat($item->sale_price) }}
            </span>
            <small class="compare-price">
                <s>{{ currencyformat($item->regular_price) }}</s>
            </small>
            <span class="price-discount-percent">
                {{ $item->discountPercentage() }}% Off
            </span>
        @else
            <span class="price-sale">
                {{ currencyformat($item->regular_price) }}
            </span>
        @endif
    @endif

</div>
