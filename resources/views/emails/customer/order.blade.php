@extends('emails.layouts.master')

@section('content')

    <p>Dear {{ $order->customer_name }},</p>

    <p>
        Your order has been updated successfully.
        Please find the latest details below.
    </p>

    <div class="row">
        <span class="label">Order Number</span>
        <div class="value"><strong>#{{ $order->order_number }}</strong></div>
    </div>

    <div class="row">
        <span class="label">Current Status</span>
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
        <span class="label">Items Ordered</span>
        <div class="value">
            @foreach($order->items as $item)
                <div style="padding:8px 0;">
                    <strong>{{ $item->product_name }}</strong>
                    @if($item->variant_name)
                        ({{ $item->variant_name }})
                    @endif
                    <br>
                    Qty: {{ $item->quantity }}
                </div>

                @if(!$loop->last)
                    <hr style="border:none; border-top:1px solid #e5e7eb;">
                @endif
            @endforeach
        </div>
    </div>

    <div class="row">
        <span class="label">Total Amount</span>
        <div class="value"><strong>₹{{ number_format($order->total, 2) }}</strong></div>
    </div>

    <p>
        Thank you for shopping with us.
    </p>

@endsection