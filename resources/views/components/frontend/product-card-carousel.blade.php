@php
    $imgurl = $item->image ? $item->image_url : asset('frontend/images/product.webp');
@endphp
<div class="item">
    <div class="product-box">
        <figure>
            <a href="{{ route('products.details', $item->slug) }}" class="href">
                <img src="{{ $imgurl }}" alt="{{ $item->image_alt ?? $item->title }}">
            </a>
        </figure>
        <div class="product-btns">
            <x-frontend.add-to-cart :cartQty="$item->cart_qty" :product="$item" :isSingle="false" />
            <x-frontend.add-to-wishlist :product="$item" :isSingle="false" />
        </div>
        @if ($item->sale_price)
            <div class="custom-tags-home">
                <p>Sale</p>
            </div>
        @endif
        <h4>
            <a href="{{ route('products.details', $item->slug) }}" class="href">{{ $item->title }}</a>
        </h4>
        <x-frontend.price :item="$item" />
        <!-- {{ $item->avg_rating }} -->
         
        <div class="product-rating">
            <div class="rating-starss d-inline-flex flex-row text-secondary" style="font-size: 13px;">
               @php
                    $rating = min(5, max(0, $item->avg_rating ?? 0));
                    $full   = (int) floor($rating);
                    $half   = (($rating - $full) >= 0.5) ? 1 : 0;
                    $empty  = 5 - $full - $half;
                @endphp

                @for($i = 0; $i < $full; $i++)
                    <i class="fa fa-star text-warning"></i>
                @endfor

                @if($half)
                    <i class="fa fa-star-half-alt text-warning"></i>
                @endif

                @for($i = 0; $i < $empty; $i++)
                    <i class="fa fa-star "></i>
                @endfor
            </div>
            <span class="rating-count">( {{ $item->reviews->count() ?? 0 }} Reviews)</span>
        </div>
    </div>
</div>