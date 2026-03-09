@extends('layouts.frontend')


@section('meta')
    {{-- <x-frontend-meta :model="$page" /> --}}
@endsection

@section('content')
    <!-- checkout page section start here -->
    <section class="cart-page-sec">
        <div class="container">
            {{-- <x-frontend.checkout-coupon /> --}}
            <form class="py-6" action="{{ route('checkout.process') }}" id="checkoutForm">
                <div class="row g-4 ">
                    @if ($cart->items->count() > 0)

                        <div class="col-lg-6">
                            <x-frontend.checkout-login />
                            <x-frontend.checkout-register :billingAddress="$billingAddress"
                                :shippingAddress="$shippingAddress" />
                        </div>

                        <!-- ORDER SUMMARY -->
                        <div class="col-lg-6">
                            <x-frontend.checkout-order-summary :cart="$cart" />
                        </div>

                    @else
                        <div class="text-center py-4">
                            <h4> Your cart is empty.</h4>
                            <br>
                            <a href="{{ route('page', 'shop') }}" class="btn mybtn mt-2">
                                Continue Shopping
                            </a>
                        </div>
                    @endif
                </div>
            </form>

        </div>
    </section>

    <!-- checkout page section end here -->
@endsection
@push('scripts')
    <x-frontend.intl-tel-input />
    <x-frontend.checkout-js />
@endpush