@extends('emails.layouts.customer')

@section('content')

<p style="margin-bottom: 15px;">
    Hi <strong>{{ $order->customer_name }}</strong>,
</p>

<p style="margin-bottom: 20px;">
    Your order status has been updated.
</p>

<div class="row">
    <span class="label">Order Number</span>
    <div class="value">#{{ $order->order_number }}</div>
</div>

<div class="row">
    <span class="label">Current Status</span>
    <div class="value">
        @switch($status)
            @case('pending')
                🟡 Pending
                @break

            @case('processing')
                ⏳ Processing
                @break

            @case('confirmed')
                ✅ Confirmed
                @break

            @case('shipped')
                🚚 Shipped
                @break

            @case('delivered')
                📦 Delivered
                @break

            @case('completed')
                🎉 Completed
                @break

            @case('cancelled')
                ❌ Cancelled
                @break

            @case('returned')
                ↩️ Returned
                @break

            @default
                📦 Updated
        @endswitch
    </div>
</div>

{{-- Optional message based on status --}}
<div style="margin-top: 20px; padding: 15px; background: #f9fafb; border-radius: 6px;">
    @if($status === 'processing')
        Your order is being prepared and will be shipped soon.
    @elseif($status === 'shipped')
        Good news! Your order is on the way 🚚
    @elseif($status === 'delivered')
        Your order has been delivered successfully 🎉
    @elseif($status === 'completed')
        Thank you for shopping with us 🙏
    @elseif($status === 'cancelled')
        Your order has been cancelled. If you have questions, please contact support.
    @else
        We’ll keep you updated as your order progresses.
    @endif
</div>

{{-- Order Items --}}
@if($order->relationLoaded('items'))
<div class="row" style="margin-top: 25px;">
    <span class="label">Order Items</span>

    <div class="value">
        @foreach($order->items as $item)
            <div style="margin-bottom: 10px;">
                <strong>{{ $item->product_name }}</strong>
                @if($item->variant_name)
                    ({{ $item->variant_name }})
                @endif

                <br>

                Qty: {{ $item->quantity }} |
                Price: ₹{{ number_format($item->price, 2) }}
            </div>

            @if(!$loop->last)
                <hr style="border: none; border-top: 1px solid #e5e7eb;">
            @endif
        @endforeach
    </div>
</div>
@endif

<div class="row">
    <span class="label">Total Amount</span>
    <div class="value">
        <strong>₹{{ number_format($order->total, 2) }}</strong>
    </div>
</div>

<div class="row">
    <span class="label">Payment Status</span>
    <div class="value">{{ ucfirst($order->payment_status) }}</div>
</div>

<p style="margin-top: 25px;">
    Thank you for shopping with us! 💙
</p>

@endsection