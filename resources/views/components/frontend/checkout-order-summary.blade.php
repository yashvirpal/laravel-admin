{{-- resources/views/frontend/checkout.blade.php --}}

<div class="order-summary">
    <h5 class="summary-title">
        <i class="fas fa-shopping-cart"></i>
        Your Order
    </h5>

    <table class="summary-table">
        <thead>
            <tr>
                <th>Product</th>
                <th style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cart->items as $item)
                <x-frontend.checkout-product :item="$item" />
            @endforeach

            {{-- Subtotal --}}
            <tr class="summary-row">
                <td style="color: #000000;">Subtotal</td>
                <td style="text-align: right; color: #000000;" id="checkout-subtotal" data-raw-value="{{ $cart->subtotal }}">
                    {{ currencyformat($cart->subtotal) }}
                </td>
            </tr>

            {{-- Discount (if any coupons applied) --}}
            @if($cart->coupons->isNotEmpty())
                <tr class="summary-row discount-row">
                    <td style="color: #28a745;">
                        Discount
                        <div class="applied-coupons-small mt-1">
                            @foreach($cart->coupons as $coupon)
                                <span class="badge bg-success me-1">
                                    {{ $coupon->code }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td style="text-align: right; color: #28a745;" id="checkout-discount" data-raw-value="{{ $cart->discount_total }}">
                        -{{ currencyformat($cart->discount_total) }}
                    </td>
                </tr>
            @endif

            {{-- Tax --}}
            @if($cart->tax_total > 0)
                <tr class="summary-row">
                    <td style="color: #666;">Tax ({{ $cart->tax_rate }}%)</td>
                    <td style="text-align: right; color: #666;" id="checkout-tax" data-raw-value="{{ $cart->tax_total }}">
                        {{ currencyformat($cart->tax_total) }}
                    </td>
                </tr>
            @endif

            {{-- Shipping --}}
            <tr class="summary-row">
                <td style="color: #666;">Shipping</td>
                <td style="text-align: right; color: #666;" id="checkout-shipping" data-raw-value="{{ $cart->shipping_total ?? 0 }}">
                    {{ currencyformat($cart->shipping_total ?? 0) }} 
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Shipping Options --}}
    <div class="shipping-options">
        <div class="shipping-header">
            <span><i class="fas fa-shipping-fast"></i> Shipping Method</span>
        </div>
        @foreach (enabledShippingMethods() as $sm => $smData)
            <div class="shipping-option">
                <input type="radio" 
                       id="sm_{{ $sm }}" 
                       name="shipping" 
                       value="{{ $sm }}"
                       data-amount="{{ $smData['amount'] }}"
                       data-label="{{ labelFromKey($sm) }}"
                       {{ $loop->first ? 'checked' : '' }}
                       onchange="updateShipping(this)">
                <label for="sm_{{ $sm }}">
                    {{ labelFromKey($sm) }}
                    <span class="shipping-price">({{ currencyformat($smData['amount']) }})</span>
                </label>
            </div>
        @endforeach
    </div>

    {{-- Total Section --}}
    <div class="total-section">
        <div class="total-row">
            <span>Total</span>
            <span class="total-amount" id="checkout-total" data-raw-value="{{ $cart->grand_total }}">
                {{ currencyformat($cart->grand_total) }}
            </span>
        </div>
    </div>

    {{-- Payment Methods --}}
    <div class="payment-methods">
        <h6 class="mb-3">
            <i class="fas fa-credit-card"></i> Payment Method
        </h6>
        @foreach (enabledPaymentGateways() as $pg => $pgData)
            <div class="payment-option">
                <div class="payment-header">
                    <input type="radio" 
                           id="pg_{{ strtolower($pg) }}" 
                           name="payment" 
                           value="{{ $pg }}"
                           {{ $loop->first ? 'checked' : '' }}>
                    <label for="pg_{{ strtolower($pg) }}">
                        {{ strtoupper($pg) }}
                    </label>
                </div>
                @if ($pgData['description'])
                    <p class="payment-description">{{ $pgData['description'] }}</p>
                @endif
            </div>
        @endforeach
    </div>

    <button type="submit" class="checkout-button">
        <i class="fas fa-lock"></i> Process to Checkout
    </button>
</div>

@push('scripts')
<script>
const CURRENCY_SYMBOL = '{{ config("app.currency_symbol", "₹") }}';

/**
 * Format currency with null safety
 */
function formatCurrency(value) {
    // Handle null, undefined, NaN, or non-numeric values
    if (value === null || value === undefined || value === '' || isNaN(value)) {
        value = 0;
    }
    return CURRENCY_SYMBOL + parseFloat(value).toFixed(2);
}

/**
 * Update shipping method and recalculate total
 */
function updateShipping(radio) {
    const shippingCost = parseFloat(radio.dataset.amount) || 0;
    const shippingMethod = radio.value;
    const shippingLabel = radio.dataset.label;
    
    console.log('Updating shipping:', {
        method: shippingMethod,
        label: shippingLabel,
        cost: shippingCost
    });

    showLoader();

    fetch(route('checkout.shipping'), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            shipping_method: shippingMethod,
            shipping_label: shippingLabel,
            shipping_cost: shippingCost
        })
    })
    .then(res => res.json())
    .then(data => {
        console.log('Shipping update response:', data);
        
        if (data.status === false) {
            toastr.error(data.message || 'Failed to update shipping');
            return;
        }

        // Update shipping display
        const shippingEl = document.getElementById('checkout-shipping');
        if (shippingEl) {
            const shippingValue = data.cart_shipping_raw || 0;
            shippingEl.textContent = formatCurrency(shippingValue);
            shippingEl.dataset.rawValue = shippingValue;
        }

        // Update tax if it changed
        const taxEl = document.getElementById('checkout-tax');
        if (taxEl && data.cart_tax_raw !== undefined) {
            const taxValue = data.cart_tax_raw || 0;
            taxEl.textContent = formatCurrency(taxValue);
            taxEl.dataset.rawValue = taxValue;
        }

        // Update total
        const totalEl = document.getElementById('checkout-total');
        if (totalEl) {
            const totalValue = data.cart_total_raw || 0;
            totalEl.textContent = formatCurrency(totalValue);
            totalEl.dataset.rawValue = totalValue;
        }

        toastr.success('Shipping method updated');
    })
    .catch(err => {
        console.error('Shipping update error:', err);
        toastr.error('Failed to update shipping method');
    })
    .finally(hideLoader);
}

/**
 * Initialize on page load
 */
document.addEventListener('DOMContentLoaded', function() {
    // Fix any NaN displays on page load
    document.querySelectorAll('[id^="checkout-"]').forEach(el => {
        const text = el.textContent.trim();
        if (text.includes('NaN') || text === '₹' || text === '') {
            const rawValue = parseFloat(el.dataset.rawValue) || 0;
            el.textContent = formatCurrency(rawValue);
        }
    });

    
});
</script>
@endpush