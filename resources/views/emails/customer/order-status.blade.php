@extends('emails.layouts.customer')

@section('content')

<p>Hi {{ $order->customer_name }},</p>

<p>Your order status has been updated.</p>

<div class="row">
    <span class="label">Order Number</span>
    <div class="value">#{{ $order->order_number }}</div>
</div>

<div class="row">
    <span class="label">Status</span>
    <div class="value">{{ ucfirst($status) }}</div>
</div>

<div style="margin-top: 20px; padding: 12px; background: #f9fafb; border-radius: 6px;">
    @switch($status)
        @case('processing')
            Your order is being prepared.
            @break

        @case('shipped')
            Your order is on the way 🚚
            @break

        @case('delivered')
            Your order has been delivered 🎉
            @break

        @case('completed')
            Thank you for shopping with us 🙏
            @break

        @case('cancelled')
            Your order has been cancelled.
            @break

        @default
            We will keep you updated.
    @endswitch
</div>

<div style="margin-top: 25px;">
    <a href="{{ route('order.track', encrypt($order->id)) }}"
       style="padding:10px 15px;background:#111827;color:#fff;text-decoration:none;border-radius:6px;">
        View Order
    </a>
</div>

@endsection