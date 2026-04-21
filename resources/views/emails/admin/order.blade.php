@extends('emails.layouts.master')

@section('content')

    <p style="margin-top:0;">
        A new order update has been triggered.
    </p>

    <div class="row">
        <span class="label">Order Status</span>
        <div class="value">
            @switch($order->status)
                @case('pending') 🟡 Pending @break
                @case('processing') ⏳ Processing @break
                @case('shipped') 🚚 Shipped @break
                @case('delivered') ✅ Delivered @break
                @case('completed') 🎉 Completed @break
                @case('cancelled') ❌ Cancelled @break
                @case('returned') 💸 Returned @break
                @default 📦 Updated
            @endswitch
        </div>
    </div>

    <div class="row">
        <span class="label">Order Number</span>
        <div class="value"><strong>#{{ $order->order_number }}</strong></div>
    </div>

    <div class="row">
        <span class="label">Customer</span>
        <div class="value">
            {{ $order->customer_name }}<br>
            {{ $order->customer_email }}
            @if($order->customer_phone)
                <br>{{ $order->customer_phone }}
            @endif
        </div>
    </div>

    @if($order->shipping_address)
        <div class="row">
            <span class="label">Shipping Address</span>
            <div class="value">{{ $order->shipping_address }}</div>
        </div>
    @endif

    <div class="row">
        <span class="label">Payment</span>
        <div class="value">
            Method: {{ ucfirst($order->payment_method ?? 'N/A') }}<br>
            Status: {{ ucfirst($order->payment_status) }}
        </div>
    </div>

    <div class="row">
        <span class="label">Order Items</span>
        <div class="value">
            @foreach($order->items as $item)
                <div style="padding:8px 0;">
                    <strong>{{ $item->product_name }}</strong>
                    @if($item->variant_name)
                        ({{ $item->variant_name }})
                    @endif
                    <br>
                    Qty: {{ $item->quantity }} × ₹{{ number_format($item->price, 2) }}
                    <br>
                    <strong>Subtotal: ₹{{ number_format($item->subtotal, 2) }}</strong>
                </div>

                @if(!$loop->last)
                    <hr style="border:none; border-top:1px solid #e5e7eb;">
                @endif
            @endforeach
        </div>
    </div>

    <div class="row">
        <span class="label">Order Summary</span>
        <div class="value">
            Subtotal: ₹{{ number_format($order->subtotal, 2) }}<br>
            Discount: ₹{{ number_format($order->discount_total, 2) }}<br>
            Tax: ₹{{ number_format($order->tax_total, 2) }}<br>
            Shipping: ₹{{ number_format($order->shipping_total, 2) }}<br>
            <hr style="border:none; border-top:1px solid #e5e7eb;">
            <strong>Total: ₹{{ number_format($order->total, 2) }}</strong>
        </div>
    </div>

    <div class="row">
        <span class="label">Order Date</span>
        <div class="value">{{ $order->created_at->format('d M Y h:i A') }}</div>
    </div>

@endsection