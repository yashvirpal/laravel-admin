@extends('emails.admin.layout')

@section('content')

<div class="row">
    <span class="label">Order Update</span>
    <div class="value">
        @switch($order->status)
            @case('pending')
                🟡 Pending Order
                @break

            @case('processing')
                ⏳ Processing
                @break

            @case('shipped')
                🚚 Shipped
                @break

            @case('delivered')
                ✅ Delivered
                @break

            @case('completed')
                🎉 Completed
                @break

            @case('cancelled')
                ❌ Cancelled
                @break

            @case('returned')
                💸 Returned
                @break

            @default
                📦 Order Update
        @endswitch
    </div>
</div>

<div class="row">
    <span class="label">Order Number</span>
    <div class="value">#{{ $order->order_number }}</div>
</div>

<div class="row">
    <span class="label">Customer Name</span>
    <div class="value">{{ $order->customer_name }}</div>
</div>

<div class="row">
    <span class="label">Customer Email</span>
    <div class="value">{{ $order->customer_email }}</div>
</div>

@if($order->customer_phone)
<div class="row">
    <span class="label">Phone</span>
    <div class="value">{{ $order->customer_phone }}</div>
</div>
@endif

@if($order->shipping_address)
<div class="row">
    <span class="label">Shipping Address</span>
    <div class="value">{{ $order->shipping_address }}</div>
</div>
@endif

@if($order->billing_address)
<div class="row">
    <span class="label">Billing Address</span>
    <div class="value">{{ $order->billing_address }}</div>
</div>
@endif

<div class="row">
    <span class="label">Payment Method</span>
    <div class="value">{{ ucfirst($order->payment_method ?? 'N/A') }}</div>
</div>

<div class="row">
    <span class="label">Payment Status</span>
    <div class="value">{{ ucfirst($order->payment_status) }}</div>
</div>

<div class="row">
    <span class="label">Shipping Method</span>
    <div class="value">{{ $order->shipping_method ?? 'N/A' }}</div>
</div>

<div class="row">
    <span class="label">Order Status</span>
    <div class="value">{{ ucfirst($order->status) }}</div>
</div>

@if($order->transaction_id)
<div class="row">
    <span class="label">Transaction ID</span>
    <div class="value">{{ $order->transaction_id }}</div>
</div>
@endif

<div class="row">
    <span class="label">Order Items</span>
    <div class="value">
        @foreach($order->items as $item)
            <div style="margin-bottom: 12px;">
                <strong>{{ $item->product_name }}</strong>

                @if($item->variant_name)
                    ({{ $item->variant_name }})
                @endif

                <br>

                Qty: {{ $item->quantity }}

                <br>

                Price: ₹{{ number_format($item->price, 2) }}

                <br>

                Subtotal: ₹{{ number_format($item->subtotal, 2) }}
            </div>

            @if(!$loop->last)
                <hr style="border:none; border-top:1px solid #e5e7eb;">
            @endif
        @endforeach
    </div>
</div>

<div class="row">
    <span class="label">Subtotal</span>
    <div class="value">₹{{ number_format($order->subtotal, 2) }}</div>
</div>

<div class="row">
    <span class="label">Discount</span>
    <div class="value">₹{{ number_format($order->discount_total, 2) }}</div>
</div>

<div class="row">
    <span class="label">Tax</span>
    <div class="value">₹{{ number_format($order->tax_total, 2) }}</div>
</div>

<div class="row">
    <span class="label">Shipping</span>
    <div class="value">₹{{ number_format($order->shipping_total, 2) }}</div>
</div>

<div class="row">
    <span class="label">Total Amount</span>
    <div class="value"><strong>₹{{ number_format($order->total, 2) }}</strong></div>
</div>

@if($order->notes)
<div class="row">
    <span class="label">Notes</span>
    <div class="value">{{ $order->notes }}</div>
</div>
@endif

<div class="row">
    <span class="label">Order Date</span>
    <div class="value">{{ $order->created_at->format('d M Y h:i A') }}</div>
</div>

@endsection

Order #{$order->order_number} - {$order->status}