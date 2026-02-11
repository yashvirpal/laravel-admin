{{-- resources/views/frontend/product-details.blade.php --}}

@extends('layouts.frontend')

@section('meta')
    {{-- <x-frontend-meta :model="$page" /> --}}
@endsection

@section('content')
    <!-- products details section start here -->
    <section class="product-section py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6">
                    <!-- MAIN IMAGE -->
                    <div class="prod_img_outer prod_img2 has_bigger">
                        @php
                            $imgurl = $product->image ? $product->image_url : asset('frontend/images/product.webp');
                        @endphp
                        <div class="prod_img">
                            <span class="setup main-image">
                                @if ($product->sale_price)
                                    <div class="custom-tags-home">
                                        <p>Sale</p>
                                    </div>
                                @endif
                                <img id="mainImg" src="{{ $imgurl }}" alt="{{ $product->image_alt ?? $product->title }}">
                            </span>
                        </div>
                        @if($product->galleries->isNotEmpty())
                            <!-- THUMBNAIL SLIDER + ARROWS -->
                            <div class="thumb-slider-wrapper">
                                <button class="thumb-arrow prev-thumb" type="button">▲</button>
                                <div class="owl-carousel owl-thumbs">
                                    @foreach ($product->galleries as $gallery)
                                        @php
                                            $thumbUrl = $gallery->image ? $gallery->image_url : asset('frontend/images/product.webp');
                                        @endphp
                                        <div class="thumb">
                                            <img src="{{ $thumbUrl }}" data-large="{{ $thumbUrl }}"
                                                alt="{{ $gallery->alt ?? $product->title }}">
                                        </div>
                                    @endforeach
                                </div>
                                <button class="thumb-arrow next-thumb" type="button">▲</button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- RIGHT SIDE PRODUCT INFO -->
                <div class="col-lg-6">
                    <div class="product-info">
                        <h2>{{ $product->title }}</h2>

                        @if($product->has_variants)
                            <!-- VARIANT SELECTION -->
                            <div class="row mb-3" id="variant-attributes">
                                @foreach ($product->attributes as $attr)
                                    <div class="col-md-6 mb-3">
                                        <label class="fw-bold">{{ $attr->name }}</label>
                                        <select class="variant-select form-select" data-attr="{{ $attr->id }}">
                                            <option value="">Select {{ $attr->name }}</option>
                                            @foreach ($attr->values as $val)
                                                <option value="{{ $val->id }}">{{ $val->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>

                            @php
                                $min = $product->variants->min('sale_price') ?? $product->variants->min('regular_price');
                                $max = $product->variants->max('sale_price') ?? $product->variants->max('regular_price');
                            @endphp

                            <!-- PRICE DISPLAY -->
                            <div class="price ffdd mb-3">
                                <span id="price-range">
                                    {{ currencyformat($min) }} - {{ currencyformat($max) }}
                                </span>
                                <div id="variant-price" style="display:none;"></div>
                            </div>

                            <!-- VARIANT STOCK STATUS -->
                            <div id="variant-stock-status" class="mb-3" style="display:none;">
                                <span class="badge bg-success" id="stock-badge">In Stock</span>
                            </div>
                        @else
                            <!-- SIMPLE PRODUCT PRICE -->
                            <div class="price ffdd mb-3">
                                @if ($product->sale_price)
                                    <span>
                                        {{ currencyformat($product->sale_price) }}
                                    </span>
                                    <small class="compare-price">
                                        <s>{{ currencyformat($product->regular_price) }}</s>
                                    </small>
                                    <span class="prepaid-offer">
                                        {{ $product->discountPercentage() }}% Off
                                    </span>
                                @else
                                    <span class="price-sale">
                                        {{ currencyformat($product->regular_price) }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        <p>{{ $product->short_description }}</p>

                        @if ($product->tags->isNotEmpty())
                            <div class="custom_tag_pdp">
                                <ul>Tags:
                                    @foreach ($product->tags as $tag)
                                        <li class="tags">{{ $tag->title }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($product->categories->isNotEmpty())
                            <div class="product-category-box">
                                Category:
                                @foreach ($product->categories as $category)
                                    <a href="{{ route('products.list', $category->slug) }}">
                                        {{ $category->title }}
                                    </a>@if(!$loop->last),@endif
                                @endforeach
                            </div>
                        @endif

                        <!-- QUANTITY & ADD TO CART -->
                        <div class="quantity-box pro-h">
                            <label class="fw-bold">Quantity:</label>
                            <div class="quantity-wrap">
                                <div class="gap-3 mt-1">
                                    <div class="qty-wrapper" id="qtywrapper{{ $product->id }}">
                                        <button type="button" class="qty-btn" data-type="minus">-</button>
                                        <input type="number" class="qty-input" value="1" min="1" max="100">
                                        <button type="button" class="qty-btn" data-type="plus">+</button>
                                    </div>
                                </div>
                                <div class="btn-box">
                                    @if($product->has_variants)
                                        <button class="btn btn-primary" id="addCart{{ $product->id }}" 
                                                onclick="addVariantToCart({{ $product->id }})" disabled>
                                            <i class="bi bi-cart-plus me-1"></i> Select Options
                                        </button>
                                        <button class="btn btn-success" id="buyNow{{ $product->id }}" 
                                                onclick="addVariantToCart({{ $product->id }}, true)" disabled>
                                            <i class="bi bi-lightning-fill me-1"></i> Buy Now
                                        </button>
                                    @else
                                        @php
                                            $inCart = isset($product->cart_qty) && $product->cart_qty > 0;
                                        @endphp
                                        <button class="btn btn-primary addCart{{ $product->id }}" 
                                                id="addCart{{ $product->id }}" 
                                                onclick="addToCart({{ $product->id }}, 1, false, null)"
                                                {{ $inCart ? 'disabled' : '' }}>
                                            <i class="bi bi-cart-plus me-1"></i> 
                                            {{ $inCart ? 'Added to Cart' : 'Add to Cart' }}
                                        </button>
                                        <button class="btn btn-success" id="buyNow{{ $product->id }}" 
                                                onclick="addToCart({{ $product->id }}, 1, true, null)"
                                                {{ $inCart ? 'disabled' : '' }}>
                                            <i class="bi bi-lightning-fill me-1"></i> Buy Now
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <x-frontend.add-to-wishlist :product="$product" :isSingle="true" />
                        </div>

                        <!-- ds-memonics -->
                        <div class="ds-memonics">
                            <ul>
                                <li>
                                    <figure><img src="{{ asset('frontend/assets/images/ds1.webp') }}" alt="Free Delivery"></figure>
                                    <h4>Free Delivery</h4>
                                </li>
                                <li>
                                    <figure><img src="{{ asset('frontend/assets/images/ds2.webp') }}" alt="7 Day Return"></figure>
                                    <h4>7 Day Return</h4>
                                </li>
                                <li>
                                    <figure><img src="{{ asset('frontend/assets/images/ds3.webp') }}" alt="100% Authentic"></figure>
                                    <h4>100% Authentic</h4>
                                </li>
                                <li>
                                    <figure><img src="{{ asset('frontend/assets/images/ds4.webp') }}" alt="Secure Payment"></figure>
                                    <h4>Secure Payment</h4>
                                </li>
                            </ul>
                        </div>

                        <div class="custom-html">
                            <div class="bulk-order-enquiry">
                                <p>Want to buy in bulk?
                                    <a href="{{ route('page', 'bulk-enquiry') }}" style="color: var(--bg---color-bg-3)">
                                        Enquiry Now
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- products details section end here -->

    <!-- pro-details-tabs section start here -->
    <div class="container my-5">
        <!-- Tabs -->
        <div class="product-tabs">
            <button class="product-tab-btn active" data-tab="desc">Description</button>
            <button class="product-tab-btn" data-tab="reviews">Reviews <span class="count">0</span></button>
            @if($product->faqs->count())
                <button class="product-tab-btn" data-tab="faq">FAQ</button>
            @endif
        </div>

        <!-- Description -->
        <div id="desc" class="tab-content-box active">
            <h4>Description</h4>
            <div>{!! $product->description !!}</div>
        </div>

        <!-- Reviews -->
        <div id="reviews" class="tab-content-box">
            <!-- Comment List -->
            <div class="comment-list mb-5">
                <div class="comment-item">
                    <div class="comment-avatar">A</div>
                    <div class="comment-content">
                        <div class="comment-header">
                            <h6>Akash Verma</h6>
                            <span class="comment-date">June 12, 2025</span>
                        </div>
                        <div class="comment-rating">★★★★☆</div>
                        <p>Beautiful pendant, very good quality and fast delivery.</p>
                    </div>
                </div>

                <div class="comment-item">
                    <div class="comment-avatar">R</div>
                    <div class="comment-content">
                        <div class="comment-header">
                            <h6>Riya Sharma</h6>
                            <span class="comment-date">June 10, 2025</span>
                        </div>
                        <div class="comment-rating">★★★★★</div>
                        <p>Loved the crystal clarity and packaging. Highly recommended.</p>
                    </div>
                </div>
            </div>

            <!-- Review Form -->
            <div class="review-section">
                <h3 class="review-title">Add a Review</h3>
                <div class="rating-wrap mb-3">
                    <label class="fw-semibold mb-1">Your rating *</label>
                    <div class="rating-stars">
                        <i class="fas fa-star" data-index="1"></i>
                        <i class="fas fa-star" data-index="2"></i>
                        <i class="fas fa-star" data-index="3"></i>
                        <i class="fas fa-star" data-index="4"></i>
                        <i class="fas fa-star" data-index="5"></i>
                    </div>
                </div>

                <form>
                    <div class="mb-3">
                        <label class="fw-semibold mb-1">Your review *</label>
                        <textarea class="form-control" rows="4"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-semibold mb-1">Name *</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-semibold mb-1">Email *</label>
                            <input type="email" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn-review-submit">Submit</button>
                </form>
            </div>
        </div>

        @if($product->faqs->count())
            <div id="faq" class="tab-content-box">
                <h3>Product FAQs</h3>
                <div class="accordion" id="productFaqAccordion">
                    @foreach($product->faqs as $faq)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-{{ $faq->id }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse-{{ $faq->id }}" aria-expanded="false"
                                    aria-controls="collapse-{{ $faq->id }}">
                                    {{ $faq->question }}
                                </button>
                            </h2>
                            <div id="collapse-{{ $faq->id }}" class="accordion-collapse collapse"
                                aria-labelledby="heading-{{ $faq->id }}" data-bs-parent="#productFaqAccordion">
                                <div class="accordion-body">
                                    {{ $faq->answer }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <!-- pro-details-tabs section end here -->

    @if($relatedProducts->isNotEmpty())
        <!-- Related Products section start here -->
        <section class="bracelets-sec bg-white pt-0">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="section-title text-center">Related Products</h2>
                        <div class="bracelets-box">
                            <div class="owl-carousel products-silder owl-theme">
                                @foreach ($relatedProducts as $item)
                                    <x-frontend.product-card-carousel :item="$item" />
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Related Products section end here -->
    @endif
@endsection

@push('scripts')
<script>
let selectedVariantId = null;
const productId = {{ $product->id }};
const hasVariants = {{ $product->has_variants ? 'true' : 'false' }};

@if($product->has_variants)
/**
 * Handle variant selection
 */
document.querySelectorAll('.variant-select').forEach(select => {
    select.addEventListener('change', function() {
        // Get all selected attribute values
        let values = [];
        let allSelected = true;

        document.querySelectorAll('.variant-select').forEach(s => {
            if (s.value === "") {
                allSelected = false;
            } else {
                values.push(parseInt(s.value));
            }
        });

        // If not all attributes selected, show price range
        if (!allSelected) {
            document.getElementById('variant-price').style.display = 'none';
            document.getElementById('price-range').style.display = 'inline';
            document.getElementById('variant-stock-status').style.display = 'none';
            
            // Disable add to cart buttons
            disableCartButtons();
            return;
        }

        // Fetch variant details
        fetchVariantDetails(values);
    });
});

/**
 * Fetch variant details from server
 */
function fetchVariantDetails(values) {
    fetch('/get-variant-price', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            values: values
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.found) {
            selectedVariantId = data.variant_id;
            
            // Update price display
            let priceHtml = '';
            if (data.sale_price) {
                const discount = Math.round(((data.regular_price - data.sale_price) / data.regular_price) * 100);
                priceHtml = `
                    <span>${data.sale_formatted}</span>
                    <small class="compare-price"><s>${data.regular_formatted}</s></small>
                    <span class="prepaid-offer">${discount}% Off</span>
                `;
            } else {
                priceHtml = `<span>${data.regular_formatted}</span>`;
            }

            document.getElementById('price-range').style.display = 'none';
            document.getElementById('variant-price').style.display = 'inline';
            document.getElementById('variant-price').innerHTML = priceHtml;

            // Update stock status
            const stockStatus = document.getElementById('variant-stock-status');
            const stockBadge = document.getElementById('stock-badge');
            
            if (data.stock > 0) {
                stockBadge.className = 'badge bg-success';
                stockBadge.textContent = `In Stock (${data.stock} available)`;
                enableCartButtons();
            } else {
                stockBadge.className = 'badge bg-danger';
                stockBadge.textContent = 'Out of Stock';
                disableCartButtons();
            }
            stockStatus.style.display = 'block';

            // Update quantity max
            const qtyInput = document.querySelector('.qty-input');
            if (qtyInput) {
                qtyInput.max = data.stock;
                if (parseInt(qtyInput.value) > data.stock) {
                    qtyInput.value = data.stock;
                }
            }
        } else {
            toastr.error('This variant is not available');
            disableCartButtons();
        }
    })
    .catch(err => {
        console.error('Error fetching variant:', err);
        toastr.error('Failed to load variant details');
        disableCartButtons();
    });
}

/**
 * Enable cart buttons
 */
function enableCartButtons() {
    const addBtn = document.getElementById(`addCart${productId}`);
    const buyBtn = document.getElementById(`buyNow${productId}`);
    
    if (addBtn) {
        addBtn.disabled = false;
        addBtn.innerHTML = '<i class="bi bi-cart-plus me-1"></i> Add to Cart';
    }
    if (buyBtn) {
        buyBtn.disabled = false;
        buyBtn.innerHTML = '<i class="bi bi-lightning-fill me-1"></i> Buy Now';
    }
}

/**
 * Disable cart buttons
 */
function disableCartButtons() {
    selectedVariantId = null;
    
    const addBtn = document.getElementById(`addCart${productId}`);
    const buyBtn = document.getElementById(`buyNow${productId}`);
    
    if (addBtn) {
        addBtn.disabled = true;
        addBtn.innerHTML = '<i class="bi bi-cart-plus me-1"></i> Select Options';
    }
    if (buyBtn) {
        buyBtn.disabled = true;
    }
}
@endif

/**
 * Handle quantity increment/decrement buttons
 */
// document.addEventListener('click', function(e) {
//     const btn = e.target.closest('.qty-btn');
//     if (!btn) return;

//     const wrapper = btn.closest('.qty-wrapper');
//     if (!wrapper) return;

//     const input = wrapper.querySelector('.qty-input');
//     if (!input) return;

//     let value = parseInt(input.value) || 1;
//     const min = parseInt(input.getAttribute('min')) || 1;
//     const max = parseInt(input.getAttribute('max')) || 100;

//     if (btn.dataset.type === 'plus' && value < max) {
//         input.value = value + 1;
//     } else if (btn.dataset.type === 'minus' && value > min) {
//         input.value = value - 1;
//     }
// });

/**
 * Tab switching
 */
document.querySelectorAll('.product-tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove active class from all
        document.querySelectorAll('.product-tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content-box').forEach(box => box.classList.remove('active'));

        // Add active to clicked
        this.classList.add('active');
        const tabId = this.dataset.tab;
        document.getElementById(tabId).classList.add('active');
    });
});

/**
 * Star rating selection
 */
document.querySelectorAll('.rating-stars i').forEach(star => {
    star.addEventListener('click', function() {
        const rating = this.dataset.index;
        document.querySelectorAll('.rating-stars i').forEach((s, idx) => {
            if (idx < rating) {
                s.classList.add('active');
            } else {
                s.classList.remove('active');
            }
        });
    });
});
</script>
@endpush