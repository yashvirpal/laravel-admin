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

            <tr>
                <td style="color: #000000;">Subtotal</td>
                <td style="text-align: right; color: #000000;">{{ currencyformat($cart->grand_total) }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="shipping-options">
        <div class="shipping-header">
            <span><i class="fas fa-shipping-fast"></i> Shipping Method</span>
            <span class="price">{{ currencyformat(0) }}</span>
        </div>
        @foreach (enabledShippingMethods() as $sm => $smData)
            <div class="shipping-option">
                <input type="radio" id="sm_{{ $sm }}" name="shipping" data-amount="{{ $smData['amount'] }}">
                <label for="sm_{{ $sm }}">{{ labelFromKey($sm) }}</label>
            </div>
        @endforeach
    </div>

    <div class="total-section">
        <div class="total-row">
            <span>Total</span>
            <span class="total-amount">₹323.00</span>
        </div>
    </div>

    <div class="payment-methods">
        @foreach (enabledPaymentGateways() as $pg => $pgData)
            <div class="payment-option">
                <div class="payment-header">
                    <input type="radio" id="pg_{{ strtolower($pg) }}" name="payment">
                    <label for="pg_{{ strtolower($pg) }}">
                        {{-- <i class="fas fa-money-bill-wave"></i> --}}
                        {{ strtoupper($pg) }}
                    </label>
                </div>
                @if ($pgData['description'])
                    <p class="payment-description"> {{ $pgData['description'] ?? ""}} </p>
                @endif
            </div>
        @endforeach
    </div>

    <button type="submit" class="checkout-button">
        <i class="fas fa-lock"></i> Process to Checkout
    </button>

</div>