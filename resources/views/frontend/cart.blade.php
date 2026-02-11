{{-- resources/views/frontend/cart.blade.php --}}

@extends('layouts.frontend')

@section('meta')
    {{-- <x-frontend-meta :model="$page" /> --}}
@endsection

@section('content')
    <!-- cart page section start here -->
    <section class="cart-page-sec">
        <div class="container">
            <div class="container py-5">
                <div class="row g-4" id="cartpage">
                    @if ($cart->items->count() > 0)
                        <div class="col-lg-9">
                            <div class="table-responsive">
                                <table class="table align-middle table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th class="text-center" style="width:160px;">Quantity</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cart-items-tbody">
                                        @foreach($cart->items as $item)
                                            <x-frontend.cart-product :item="$item" />
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">
                                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                                    <!-- Applied Coupons Display -->
                                                    <div class="applied-coupons-list" id="applied-coupons-list">
                                                        @if($cart->coupons->isNotEmpty())
                                                            @foreach($cart->coupons as $coupon)
                                                            <!-- {{ $coupon }} -->
                                                                <div class="d-inline-flex align-items-center bg-success text-white px-3 py-1 rounded me-2 mb-2"
                                                                    data-coupon-id="{{ $coupon->id }}"
                                                                    data-coupon-code="{{ $coupon->code }}">
                                                                    <span class="me-2">{{ $coupon->code }}</span>
                                                                    <small class="me-2 coupon-discount-amount">
                                                                        (-{{ currencyformat($coupon->pivot->discount_amount) }})
                                                                    </small>
                                                                    <button type="button" class="btn-close btn-close-white btn-sm"
                                                                        onclick="removeCoupon({{ $coupon->id }})"
                                                                        aria-label="Remove"></button>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>

                                                    <!-- Coupon Form -->
                                                    <form class="d-flex gap-2" onsubmit="event.preventDefault(); applyCoupon();">
                                                        <input type="text" class="form-control form-control-sm" id="coupon-code"
                                                            placeholder="Coupon Code" style="text-transform: uppercase;">
                                                        <button class="btn btn-sm btn-outline-dark mybtn" type="submit" id="apply-coupon-btn">
                                                            Apply
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="border rounded p-3 shadow-sm">
                                <h5 class="fw-bold mb-3">Cart Totals</h5>

                                <table class="table table-sm mb-3">
                                    <tr>
                                        <td>Subtotal</td>
                                        <td class="text-end" id="cart-subtotal" data-raw-value="{{ $cart->subtotal }}">
                                            {{ currencyformat($cart->subtotal) }}
                                        </td>
                                    </tr>

                                    <tr class="text-success" id="discount-row" style="{{ $cart->discount_total > 0 ? '' : 'display: none;' }}">
                                        <td>Discount</td>
                                        <td class="text-end" id="cart-discount" data-raw-value="{{ $cart->discount_total }}">
                                            -{{ currencyformat($cart->discount_total) }}
                                        </td>
                                    </tr>

                                    @if($cart->tax_total > 0)
                                        <tr id="tax-row">
                                            <td>Tax ({{ $cart->tax_rate }}%)</td>
                                            <td class="text-end" id="cart-tax" data-raw-value="{{ $cart->tax_total }}">
                                                {{ currencyformat($cart->tax_total) }}
                                            </td>
                                        </tr>
                                    @else
                                        <tr id="tax-row" style="display: none;">
                                            <td>Tax (<span id="tax-rate">{{ $cart->tax_rate }}</span>%)</td>
                                            <td class="text-end" id="cart-tax" data-raw-value="0">
                                                {{ currencyformat(0) }}
                                            </td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <td>Shipping</td>
                                        <td class="text-end" id="cart-shipping" data-raw-value="{{ $cart->shipping_total }}">
                                            {{ currencyformat($cart->shipping_total) }}
                                        </td>
                                    </tr>

                                    <tr class="fw-bold">
                                        <td>Total</td>
                                        <td class="text-end" id="cart-total" data-raw-value="{{ $cart->grand_total }}">
                                            {{ currencyformat($cart->grand_total) }}
                                        </td>
                                    </tr>
                                </table>

                                <a href="{{ route('page','checkout') }}" class="btn btn-dark w-100 mybtn">
                                    Proceed to Checkout <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x display-1 text-muted"></i>
                            <h3 class="mt-3">Your cart is empty</h3>
                            <a href="/" class="btn btn-primary mt-3 mybtn">
                                Continue Shopping
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!-- cart page section end here -->
@endsection

@push('scripts')
<!-- <script>
const CURRENCY_SYMBOL = '{{ config("app.currency_symbol", "₹") }}';

/**
 * Format number as currency
 */
function formatCurrency(value) {
    return CURRENCY_SYMBOL + parseFloat(value).toFixed(2);
}

/**
 * Check if coupon is already applied
 */
function isCouponApplied(code) {
    const appliedCoupons = document.querySelectorAll('[data-coupon-code]');
    for (let coupon of appliedCoupons) {
        if (coupon.dataset.couponCode.toUpperCase() === code.toUpperCase()) {
            return true;
        }
    }
    return false;
}

/**
 * Check if cart has free items
 */
function hasFreeItems() {
    return document.querySelectorAll('tr.table-success').length > 0;
}

/**
 * Apply coupon
 */
function applyCoupon() {
    const couponInput = document.getElementById('coupon-code');
    const applyBtn = document.getElementById('apply-coupon-btn');
    
    if (!couponInput) return;

    const code = couponInput.value.trim().toUpperCase();
    if (!code) {
        toastr.warning('Please enter a coupon code');
        couponInput.focus();
        return;
    }

    if (isCouponApplied(code)) {
        toastr.warning('This coupon is already applied');
        couponInput.value = '';
        return;
    }

    applyBtn.disabled = true;
    applyBtn.textContent = 'Applying...';
    showLoader();

    fetch(route('cart.coupon.apply'), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ code: code })
    })
    .then(res => res.json())
    .then(data => {
        console.log('✅ Apply Coupon Response:', data);
        
        if (data.status === false) {
            toastr.error(data.message);
            return;
        }

        toastr.success(data.message);
        couponInput.value = '';

        // Check for free items
        if (data.free_items && data.free_items.length > 0) {
            toastr.info(`Adding ${data.free_items.length} free item(s)!`);
            setTimeout(() => location.reload(), 1500);
            return;
        }

        // Add coupon badge
        const appliedCouponsList = document.getElementById('applied-coupons-list');
        if (appliedCouponsList && data.coupon_id) {
            const couponHtml = `
                <div class="d-inline-flex align-items-center bg-success text-white px-3 py-1 rounded me-2 mb-2" 
                     data-coupon-id="${data.coupon_id}"
                     data-coupon-code="${code}">
                    <span class="me-2">${code}</span>
                    <small class="me-2 coupon-discount-amount">
                        (-${formatCurrency(data.discount_raw)})
                    </small>
                    <button type="button" 
                            class="btn-close btn-close-white btn-sm" 
                            onclick="removeCoupon(${data.coupon_id})"
                            aria-label="Remove"></button>
                </div>
            `;
            appliedCouponsList.insertAdjacentHTML('beforeend', couponHtml);
        }

        // Update totals
        updateCartTotals(data);

        // Update mini cart
        if (typeof loadMiniCart === 'function') {
            loadMiniCart();
        }
    })
    .catch(err => {
        console.error("❌ Apply Coupon Error:", err);
        toastr.error("Failed to apply coupon");
    })
    .finally(() => {
        hideLoader();
        applyBtn.disabled = false;
        applyBtn.textContent = 'Apply';
    });
}

/**
 * Remove coupon
 */
function removeCoupon(couponId) {
    if (!confirm('Remove this coupon?')) {
        return;
    }

    const hadFreeItems = hasFreeItems();
    showLoader();

    fetch(route('cart.coupon.remove', couponId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        console.log('✅ Remove Coupon Response:', data);
        
        if (data.status === false) {
            toastr.error(data.message);
            return;
        }

        toastr.success(data.message);

        // Remove coupon badge
        const couponBadge = document.querySelector(`[data-coupon-id="${couponId}"]`);
        if (couponBadge) {
            couponBadge.remove();
            console.log('✓ Removed coupon badge');
        }

        // If had free items, reload page
        if (hadFreeItems) {
            console.log('Reloading to remove free items...');
            setTimeout(() => location.reload(), 800);
            return;
        }

        // Update totals without reload
        updateCartTotals(data);

        // Update mini cart
        if (typeof loadMiniCart === 'function') {
            loadMiniCart();
        }
    })
    .catch(err => {
        console.error("❌ Remove Coupon Error:", err);
        toastr.error("Failed to remove coupon");
    })
    .finally(() => {
        if (!hadFreeItems) {
            hideLoader();
        }
    });
}

/**
 * Update cart totals
 */
function updateCartTotals(data) {
    console.log('📊 Updating Cart Totals');
    console.log('Data received:', data);

    // Update subtotal
    if (data.cart_subtotal_raw !== undefined) {
        const el = document.getElementById('cart-subtotal');
        if (el) {
            el.textContent = formatCurrency(data.cart_subtotal_raw);
            el.dataset.rawValue = data.cart_subtotal_raw;
            console.log('✓ Subtotal:', formatCurrency(data.cart_subtotal_raw));
        }
    }

    // Update discount
    if (data.cart_discount_raw !== undefined) {
        const discountEl = document.getElementById('cart-discount');
        const discountRow = document.getElementById('discount-row');
        
        if (discountEl) {
            discountEl.textContent = '-' + formatCurrency(data.cart_discount_raw);
            discountEl.dataset.rawValue = data.cart_discount_raw;
            console.log('✓ Discount:', '-' + formatCurrency(data.cart_discount_raw));
        }
        
        if (discountRow) {
            const hasDiscount = parseFloat(data.cart_discount_raw) > 0;
            discountRow.style.display = hasDiscount ? 'table-row' : 'none';
            console.log(hasDiscount ? '✓ Showing discount row' : '✓ Hiding discount row');
        }
    }

    // Update tax
    if (data.cart_tax_raw !== undefined) {
        const el = document.getElementById('cart-tax');
        const row = document.getElementById('tax-row');
        
        if (el) {
            el.textContent = formatCurrency(data.cart_tax_raw);
            el.dataset.rawValue = data.cart_tax_raw;
            console.log('✓ Tax:', formatCurrency(data.cart_tax_raw));
        }
        
        if (row) {
            row.style.display = parseFloat(data.cart_tax_raw) > 0 ? 'table-row' : 'none';
        }
    }

    // Update shipping
    if (data.cart_shipping_raw !== undefined) {
        const el = document.getElementById('cart-shipping');
        if (el) {
            el.textContent = formatCurrency(data.cart_shipping_raw);
            el.dataset.rawValue = data.cart_shipping_raw;
            console.log('✓ Shipping:', formatCurrency(data.cart_shipping_raw));
        }
    }

    // Update grand total
    if (data.cart_total_raw !== undefined) {
        const el = document.getElementById('cart-total');
        if (el) {
            el.textContent = formatCurrency(data.cart_total_raw);
            el.dataset.rawValue = data.cart_total_raw;
            console.log('✓ Total:', formatCurrency(data.cart_total_raw));
        }
    }

    console.log('✅ Cart totals updated successfully');
}

// Initialize
document.addEventListener('DOMContentLoaded', function () {
    const couponInput = document.getElementById('coupon-code');
    if (couponInput) {
        couponInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
        
        couponInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyCoupon();
            }
        });
    }
});
</script> -->
@endpush