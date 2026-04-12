@extends('layouts.frontend')

@section('content')
    <section class="account-sec">
        <div class="container mt-4 mb-5">
            <!-- <h1 class="mb-4">My Account</h1> -->

            <div class="row">
                @include('profile.partials.sidebar')

                <div class="col-lg-9 col-md-8">
                    <div class="main-content">

                        <div class="page-content">
                            <h2 class="page-title mb-4">
                                Order #{{ $order->id }}
                            </h2>

                            {{-- ================= ORDER SUMMARY ================= --}}
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <strong>Order Summary</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Date:</strong> {{ dateFormat($order->created_at) }}</p>
                                            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                                            <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
                                            <p><strong>Shipping Method:</strong> {{ $order->shipping_method ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <p><strong>Subtotal:</strong> {{ currencyformat($order->subtotal) }}</p>
                                            @if($order->discount_total > 0)
                                                <p><strong>Discount:</strong> -{{ currencyformat($order->discount_total) }}</p>
                                            @endif
                                            @if($order->tax_total > 0)
                                                <p><strong>Tax:</strong> {{ currencyformat($order->tax_total) }}</p>
                                            @endif
                                            <p><strong>Shipping:</strong>
                                                {{ $order->shipping_total > 0 ? currencyformat($order->shipping_total) : 'Free' }}
                                            </p>
                                            <hr>
                                            <p class="fs-5"><strong>Total:</strong> {{ currencyformat($order->total) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- ================= ADDRESSES ================= --}}
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <strong>Shipping & Billing</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="fw-bold">Shipping Address</h6>
                                            @if($order->shippingAddress)
                                                <p class="mb-0">{{ $order->shippingAddress->first_name }}
                                                    {{ $order->shippingAddress->last_name }}
                                                </p>
                                                <p class="mb-0">{{ $order->shippingAddress->address_line1 }}</p>
                                                @if($order->shippingAddress->address_line2)
                                                    <p class="mb-0">{{ $order->shippingAddress->address_line2 }}</p>
                                                @endif
                                                <p class="mb-0">{{ $order->shippingAddress->city }},
                                                    {{ $order->shippingAddress->state }} {{ $order->shippingAddress->zip }}
                                                </p>
                                                <p class="mb-0">{{ $order->shippingAddress->phone }}</p>
                                            @elseif($order->shipping_address)
                                                {{-- fallback to legacy JSON field --}}
                                                <p class="mb-0">{{ $order->shipping_address }}</p>
                                            @else
                                                <p class="text-muted">Not available</p>
                                            @endif
                                        </div>

                                        <div class="col-md-6 mt-3 mt-md-0">
                                            <h6 class="fw-bold">Billing Address</h6>
                                            @if($order->billingAddress)
                                                <p class="mb-0">{{ $order->billingAddress->first_name }}
                                                    {{ $order->billingAddress->last_name }}
                                                </p>
                                                <p class="mb-0">{{ $order->billingAddress->address_line1 }}</p>
                                                @if($order->billingAddress->address_line2)
                                                    <p class="mb-0">{{ $order->billingAddress->address_line2 }}</p>
                                                @endif
                                                <p class="mb-0">{{ $order->billingAddress->city }},
                                                    {{ $order->billingAddress->state }} {{ $order->billingAddress->zip }}
                                                </p>
                                                <p class="mb-0">{{ $order->billingAddress->phone }}</p>
                                            @elseif($order->billing_address)
                                                <p class="mb-0">{{ $order->billing_address }}</p>
                                            @else
                                                <p class="text-muted">Not available</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>


                            {{-- ================= PRODUCTS ================= --}}
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <strong>Ordered Products</strong>
                                </div>

                                <div class="table-responsive">
                                    <table class="table mb-0 align-middle">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Variant</th>
                                                <th>Qty</th>
                                                <th>Price</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->items as $item)
                                                <tr>
                                                    <td>
                                                        <a
                                                            href="{{ route('products.details', $item->product->slug) }}">{{ $item->product->title ?? 'N/A' }}</a>

                                                        @if($item->custom_data)
                                                            @php
                                                                $customData = is_array($item->custom_data)
                                                                    ? $item->custom_data
                                                                    : json_decode($item->custom_data, true);
                                                            @endphp

                                                            <div class="mt-2 small">
                                                                @foreach($customData as $key => $value)
                                                                    @if(!empty($value))
                                                                        <div class="d-flex">
                                                                            <span class="fw-semibold me-1">
                                                                                {{ ucwords(str_replace('_', ' ', $key)) }}:
                                                                            </span>
                                                                            <span class="text-muted">
                                                                                {{ $value }}
                                                                            </span>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        {{ $item->variant->variant_name ?? '-' }}
                                                    </td>

                                                    <td>{{ $item->quantity }}</td>

                                                    <td>
                                                        {{ currencyformat($item->price) }}
                                                    </td>

                                                    <td>
                                                        {{ currencyformat($item->price * $item->quantity) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- ================= COUPONS ================= --}}
                            @if($order->coupons->count())
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong>Applied Coupons</strong>
                                    </div>
                                    <div class="card-body">
                                        @foreach($order->coupons as $coupon)
                                            <p>
                                                <strong>Code:</strong> {{ $coupon->code }} <br>
                                                <strong>Discount:</strong>
                                                {{ currencyformat($coupon->discount_amount ?? 0) }}
                                            </p>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- ================= TRANSACTIONS ================= --}}
                            @if($order->transactions->count())
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong>Payment Transactions</strong>
                                    </div>
                                    <div class="card-body">
                                        @foreach($order->transactions as $transaction)
                                            <div class="mb-3">
                                                <p>
                                                    <strong>Transaction ID:</strong> {{ $transaction->transaction_id }} <br>
                                                    <strong>Method:</strong> {{ $transaction->payment_method }} <br>
                                                    <strong>Status:</strong> {{ ucfirst($transaction->status) }} <br>
                                                    <strong>Amount:</strong> {{ currencyformat($transaction->amount) }}
                                                </p>
                                            </div>
                                            <hr>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($order->notes))
                                <div class="mt-3 p-3 bg-light border rounded">
                                    <div class="fw-semibold mb-1">Order Notes</div>
                                    <p class="mb-0 text-muted">
                                        {{ $order->notes }}
                                    </p>
                                </div>
                            @endif


                            <a href="{{ route('profile.orders') }}" class="btn btn-outline-secondary">
                                Back to Orders
                            </a>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection