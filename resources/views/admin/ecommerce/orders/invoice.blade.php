<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->order_number }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            padding: 30px;
            color: #333;
        }

        .invoice-box {
            max-width: 1000px;
            margin: auto;
            border: 1px solid #ddd;
            padding: 30px;
            background: #fff;
        }

        .sub-heading {
            font-size: 18px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #ddd;
        }

        .row {
            width: 100%;
            display: table;
            margin-bottom: 20px;
        }

        .col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        table th {
            background: #f8f9fa;
        }

        .text-end {
            text-align: right;
        }

        .muted {
            color: #777;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .mb-1 {
            margin-bottom: 6px;
        }

        .total-row td {
            font-weight: bold;
            font-size: 15px;
            background: #f8f9fa;
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
        }

        .logo-wrapper {
            margin-bottom: 12px;
        }

        .invoice-logo {
            max-width: 180px;
            max-height: 70px;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .store-name {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .store-details p {
            margin: 3px 0;
            color: #666;
            font-size: 14px;
        }

        .footer-note {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 13px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        .address-block {
            line-height: 1.7;
            margin-top: 6px;
        }
    </style>
</head>

<body @if(($mode ?? '') === 'print') onload="window.print()" @endif>

    <div class="invoice-box">

        <!-- Store Header -->
        <div class="invoice-header">
            <div class="logo-wrapper">
                <img src="{{ asset('frontend/assets/images/logo.webp') }}" alt="{{ config('app.name') }}"
                    class="invoice-logo">
            </div>

            <div class="store-details">
                <h2 class="store-name">{{ config('app.name') }}</h2>

                @if(setting('address'))
                    <p>
                        <strong>Address:</strong>
                        {!! nl2br(e(str_replace('\n', PHP_EOL, setting('address')))) !!}
                    </p>
                @endif

                @if(setting('email'))
                    <p><strong>Email:</strong> {{ setting('email') }}</p>
                @endif

                @if(setting('phone'))
                    <p><strong>Phone:</strong> {{ setting('phone') }}</p>
                @endif
            </div>
        </div>

        <hr>

        <!-- Invoice Details -->
        <div class="sub-heading">Invoice Details</div>

        <div class="row">
            <div class="col">
                <p><strong>Invoice #:</strong> {{ $order->order_number }}</p>
                <p><strong>Order Date:</strong> {{ dateFormat($order->created_at) }}</p>
                <p><strong>Order Status:</strong> {{ ucfirst($order->status) }}</p>
                <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
            </div>

            <div class="col">
                <p><strong>Customer Name:</strong> {{ $order->customer_name }}</p>
                <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
            </div>
        </div>

        <!-- Address Section -->
        <div class="row">
            <div class="col">
                <div class="sub-heading">Billing Address</div>

                @if($order->billingAddress)
                    <div class="address-block">
                        {{ $order->billingAddress->first_name }}
                        {{ $order->billingAddress->last_name }}<br>

                        {{ $order->billingAddress->address_line1 }}<br>

                        @if($order->billingAddress->address_line2)
                            {{ $order->billingAddress->address_line2 }}<br>
                        @endif

                        {{ $order->billingAddress->city }},
                        {{ $order->billingAddress->state }}
                        {{ $order->billingAddress->zip }}<br>

                        {{ $order->billingAddress->phone }}
                    </div>
                @else
                    <div class="muted">Not available</div>
                @endif
            </div>

            <div class="col">
                <div class="sub-heading">Shipping Address</div>

                @if($order->shippingAddress)
                    <div class="address-block">
                        {{ $order->shippingAddress->first_name }}
                        {{ $order->shippingAddress->last_name }}<br>

                        {{ $order->shippingAddress->address_line1 }}<br>

                        @if($order->shippingAddress->address_line2)
                            {{ $order->shippingAddress->address_line2 }}<br>
                        @endif

                        {{ $order->shippingAddress->city }},
                        {{ $order->shippingAddress->state }}
                        {{ $order->shippingAddress->zip }}<br>

                        {{ $order->shippingAddress->phone }}
                    </div>
                @else
                    <div class="muted">Not available</div>
                @endif
            </div>
        </div>
        <!-- Order Items -->
        <div class="sub-heading">Order Items</div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th width="100">Qty</th>
                    <th width="120">Price</th>
                    <th width="120">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->product->title ?? '-' }}
                            @if(!empty($item->variant_name))
                                <div class="small text-muted">
                                    <strong>Variant:</strong> {{ $item->variant_name }}
                                </div>
                            @endif

                            @if(!empty($item->sku))
                                <div class="small text-muted">
                                    <strong>SKU:</strong> {{ $item->sku }}
                                </div>
                            @endif
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
                        <td>{{ $item->quantity }}</td>
                        <td>{{ currencyformat($item->price) }}</td>
                        <td>{{ currencyformat($item->subtotal) }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td colspan="4" class="text-end"><strong>Subtotal</strong></td>
                    <td>{{ currencyformat($order->subtotal) }}</td>
                </tr>

                <tr>
                    <td colspan="4" class="text-end"><strong>Shipping</strong></td>
                    <td>
                        {{ $order->shipping_total > 0 ? currencyformat($order->shipping_total) : 'Free' }}
                        <small>({{ $order->shipping_method ?? '' }})</small>
                    </td>
                </tr>

                @if($order->tax_total > 0)
                    <tr>
                        <td colspan="4" class="text-end"><strong>Tax</strong></td>
                        <td>{{ currencyformat($order->tax_total) }}</td>
                    </tr>
                @endif

                @if($order->discount_total > 0)
                    <tr>
                        <td colspan="4" class="text-end"><strong>Discount</strong></td>
                        <td>-{{ currencyformat($order->discount_total) }}</td>
                    </tr>
                @endif

                <tr class="total-row">
                    <td colspan="4" class="text-end"><strong>Total</strong></td>
                    <td>{{ currencyformat($order->total) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Transaction Details -->
        @if($order->transactions && $order->transactions->count())
            <div class="sub-heading">Transaction Details</div>

            <table>
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
                        <tr>
                            <td>{{ $transaction->transaction_id }}</td>
                            <td>{{ $transaction->payment_method }}</td>
                            <td>{{ currencyformat($transaction->amount) }}</td>
                            <td>{{ ucfirst($transaction->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Notes -->
        @if($order->notes)
            <div class="sub-heading">Order Notes</div>
            <p class="muted">{{ $order->notes }}</p>
        @endif

        <!-- Footer -->
        <div class="footer-note">
            Thank you for shopping with {{ config('app.name') }}.
        </div>

        @if(($mode ?? '') === 'view')
            <div class="no-print" style="margin-top:20px; text-align:center;">
                <button onclick="window.print()">Print Invoice</button>
            </div>
        @endif

    </div>

</body>

</html>