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
                                        <div class="col-md-4 mt-3 mt-md-0">
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

                                        <div class="col-md-4 mt-3 mt-md-0">
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

                                        <div class="col-md-4 mt-3 mt-md-0">
                                            <p><strong>Date:</strong> {{ dateFormat($order->created_at) }}</p>
                                            @php
                                                $orderBadge = orderStatusBadge($order->status);
                                                $paymentBadge = paymentStatusBadge($order->payment_status);
                                            @endphp
                                            <p>
                                                <strong>Order Status:</strong>
                                                <span class="badge {{ $orderBadge['class'] }}">
                                                    <i class="fa {{ $orderBadge['icon'] }}"></i>
                                                    {{ $orderBadge['text'] }}
                                                </span>
                                            </p>
                                            <p>
                                                <strong>Payment Status:</strong>
                                                <span class="badge {{ $paymentBadge['class'] }}">
                                                    <!-- <i class="fa {{ $paymentBadge['icon'] }}"></i> -->
                                                    {{ $paymentBadge['text'] }}
                                                </span>
                                            </p>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            {{-- ================= ADDRESSES ================= --}}
                            <!-- <div class="card mb-4">
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
                                                                                                                                                                            </div> -->


                            {{-- ================= PRODUCTS ================= --}}
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <strong>Order Items</strong>
                                </div>

                                <div class="table-responsive">
                                    <table class="table mb-0 align-middle">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Variant</th>
                                                <th>Qty</th>
                                                <th>Price</th>
                                                <th>Sub Total</th>
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
                                            <tr>
                                                <td colspan="4" class="text-center"><strong>Subtotal:</strong></td>
                                                <td>{{ currencyformat($order->subtotal) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-center"><strong>Shipping:</strong></td>
                                                <td>
                                                    {{ $order->shipping_total > 0 ? currencyformat($order->shipping_total) : 'Free' }}
                                                    <sub> ({{ $order->shipping_method ?? '' }})</sub>
                                                </td>
                                            </tr>
                                            @if($order->tax_total > 0)
                                                <tr>
                                                    <td colspan="4" class="text-center"><strong>Tax:</strong></td>
                                                    <td> {{ currencyformat($order->tax_total) }}</td>
                                                </tr>
                                            @endif
                                            @if($order->discount_total > 0)
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        <strong>Applied Coupons:</strong>
                                                        @foreach($order->coupons as $coupon)
                                                            @php
                                                                $code = $coupon->code;
                                                            @endphp
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        {{ currencyformat($order->discount_total) }}
                                                        <sub class="">({{ $code ?? '' }})</sub>
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($order->discount_total > 0)

                                            @endif
                                            <tr>
                                                <td colspan="4" class="text-center"><strong>Total:</strong></td>
                                                <td> {{ currencyformat($order->total) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>



                            {{-- ================= TRANSACTIONS ================= --}}
                            @if($order->transactions->count())
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong>Payment Transactions</strong>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Transaction ID</th>
                                                    <th>Method</th>                                                    
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->transactions as $transaction)
                                                    @php
                                                        $transactionBadge = transactionStatusBadge($transaction->status);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $transaction->transaction_id }}</td>
                                                        <td>{{ $transaction->payment_method }}</td>
                                                        <td>{{ currencyformat($transaction->amount) }}</td>
                                                        <td>
                                                            <span class="badge {{ $transactionBadge['class'] }}">
                                                                <!-- <i class="fa {{ $transactionBadge['icon'] }}"></i> -->
                                                                {{ $transactionBadge['text'] }}
                                                            </span>
                                                        </td>                                                        
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>                                       
                                    </div>
                                </div>
                            @endif

                            @if(!empty($order->notes))
                                <div class="my-3 p-3 bg-light border rounded">
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