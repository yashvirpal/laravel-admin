@extends('layouts.admin')

@section('content')
    @php
        $title = 'Order Details';
        $breadcrumbs = [
            'Home' => route('admin.dashboard'),
            'Orders' => route('admin.orders.index'),
            $title => ''
        ];
    @endphp

    <div class="card card-primary card-outline mb-4">
        <div class="card-header d-flex justify-content-end align-items-center">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-sm"><i
                    class="bi bi-arrow-left-circle me-1"></i> Back To List</a>
        </div>

        <div class="card-body">
            <!-- {{ $order }} -->
            <h4>Order Information</h4>
            <table class="table table-bordered mb-3">
                <tr>
                    <th>Order Number</th>
                    <td>{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <th>Customer Name</th>
                    <td>{{ $order->customer_name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $order->customer_email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $order->customer_phone }}</td>
                </tr>
                <tr>
                    <th>Billing Address</th>
                    <td>{{ $order->billing_address }}
                        {{ $order->billingAddress->address_line1 ?? '' }},
                        {{ $order->billingAddress->address_line2 ?? '' }}<br>
                        {{ $order->billingAddress->city ?? '' }},
                        {{ $order->billingAddress->state ?? '' }}<br>
                        {{ $order->billingAddress->country ?? '' }} -
                        {{ $order->billingAddress->zip ?? '' }}<br>
                        {{ $order->billingAddress->phone ?? '' }}
                    </td>
                </tr>
                <tr>
                    <th>Shipping Address</th>
                    <td>{{ $order->shipping_address }}
                        {{ $order->shippingAddress->address_line1 ?? '' }},
                        {{ $order->shippingAddress->address_line2 ?? '' }}<br>
                        {{ $order->shippingAddress->city ?? '' }},
                        {{ $order->shippingAddress->state ?? '' }}<br>
                        {{ $order->shippingAddress->country ?? '' }} -
                        {{ $order->shippingAddress->zip ?? '' }}<br>
                        {{ $order->shippingAddress->phone ?? '' }}
                    </td>
                </tr>
                <tr>
                    <th>Order Status</th>
                    <td>
                        @php
                            $status = orderStatusBadge($order->status);
                        @endphp
                        <span class="badge rounded-pill {{ $status['class'] }}">
                            <i class="bi {{ $status['admin_icon'] }} me-1"></i>
                            {{ $status['text'] }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Payment Status</th>
                    <td>
                        @php
                            $status = paymentStatusBadge($order->payment_status);
                        @endphp
                        <span class="badge rounded-pill {{ $status['class'] }}">
                            <i class="bi {{ $status['admin_icon'] }} me-1"></i>
                            {{ $status['text'] }}
                        </span>

                    </td>
                </tr>
                <tr>
                    <th>Subtotal</th>
                    <td>{{ currencyformat($order->subtotal) }}</td>
                </tr>

                @if ($order->tax_total > 0)
                    <tr>
                        <th>Tax</th>
                        <td>{{ currencyformat($order->tax_total) }}</td>
                    </tr>
                @endif
                @if($order->coupons->isNotEmpty())
                    <tr>
                        <th>Applied Coupons</th>
                        <td>
                            @foreach($order->coupons as $coupon)
                                <div class="d-flex align-items-center mb-1">

                                    <div class="text-success fw-semibold me-2">
                                        - {{ currencyformat($coupon->discount_amount ?? 0) }}
                                    </div>

                                    <div class="text-muted small">
                                        (<i class="bi bi-ticket-perforated me-1"></i> {{ $coupon->code }})
                                    </div>

                                </div>
                            @endforeach
                        </td>
                    </tr>
                @endif
                <tr>
                    <th>Shipping:</th>
                    <td>{{ $order->shipping_total > 0 ? currencyformat($order->shipping_total) : 'Free' }}</td>
                </tr>

                {{-- @if ($order->discount_total)
                <tr>
                    <th>Coupon</th>
                    <td>{{ currencyformat($order->discount_total) }}</td>
                </tr>
                @endif --}}

                <tr>
                    <th>Total</th>
                    <td>{{ currencyformat($order->total) }}</td>
                </tr>
            </table>

            <h4>Order Items</h4>
            <table class="table table-bordered mb-3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->product->title ?? '-' }}
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
                            <td>{{ currencyformat($item->price) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ currencyformat($item->subtotal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h4>Transaction</h4>
            @if($order->latestTransaction)
                <table class="table table-bordered">
                    <tr>
                        <th>Transaction ID</th>
                        <td>{{ $order->latestTransaction->transaction_id }}</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>{{ currencyformat($order->latestTransaction->amount) }}</td>
                    </tr>
                    <tr>
                        <th>Payment Method</th>
                        <td>{{ ucfirst($order->latestTransaction->payment_method) }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @php
                                $status = transactionStatusBadge($order->latestTransaction->status);
                            @endphp
                            <span class="badge rounded-pill {{ $status['class'] }}">
                                <i class="bi {{ $status['admin_icon'] }} me-1"></i>
                                {{ $status['text'] }}
                            </span>
                        </td>
                    </tr>
                </table>
                @if ($order->notes)
                    <div class="alert alert-warning d-flex align-items-start gap-2 shadow-sm rounded-3 my-3">
                        <i class="bi bi-sticky fs-5 mt-1"></i>
                        <div>
                            <strong class="d-block mb-1">Order Notes</strong>
                            <span class="text-muted">{{ $order->notes }}</span>
                        </div>
                    </div>
                @endif

            @else
                <p>No transaction recorded.</p>
            @endif
        </div>
    </div>
@endsection