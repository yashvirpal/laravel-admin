@extends('emails.layouts.master')

@section('content')

    {{-- Header --}}
    <p style="margin: 0 0 6px; font-size: 15px; font-weight: 600; color: #111827;">
        {{ ($isNewOrder ?? false) ? 'New Order Received' : 'Order Status Updated' }}
    </p>
    <p style="margin: 0 0 24px; font-size: 13px; color: #6b7280;">
        {{ ($isNewOrder ?? false) ? 'Placed' : 'Triggered' }} at {{ now()->format('d M Y, h:i A') }}
    </p>
    {{-- Status Badge --}}
    @php
        $statusMap = [
            'pending' => ['label' => 'Pending', 'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fcd34d'],
            'processing' => ['label' => 'Processing', 'color' => '#2563eb', 'bg' => '#eff6ff', 'border' => '#93c5fd'],
            'shipped' => ['label' => 'Shipped', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'border' => '#c4b5fd'],
            'delivered' => ['label' => 'Delivered', 'color' => '#059669', 'bg' => '#ecfdf5', 'border' => '#6ee7b7'],
            'completed' => ['label' => 'Completed', 'color' => '#1cab6a', 'bg' => '#ecfdf5', 'border' => '#1cab6a'],
            'cancelled' => ['label' => 'Cancelled', 'color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fca5a5'],
            'returned' => ['label' => 'Returned', 'color' => '#9333ea', 'bg' => '#faf5ff', 'border' => '#d8b4fe'],
        ];
        $status = $statusMap[$order->status] ?? ['label' => ucfirst($order->status), 'color' => '#374151', 'bg' => '#f9fafb', 'border' => '#d1d5db'];
    @endphp

    <div
        style="background: {{ $status['bg'] }}; border: 1px solid {{ $status['border'] }}; border-left: 4px solid {{ $status['color'] }}; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px;">
        <p style="margin: 0 0 4px; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
            Current Status</p>
        <p style="margin: 0; font-size: 20px; font-weight: 700; color: {{ $status['color'] }};">
            {{ $status['label'] }}
        </p>
    </div>

    {{-- Order Details --}}
    <p style="margin: 0 0 10px; font-size: 15px; font-weight: 700; color: #111827;">Order Details</p>

    <table width="100%" cellpadding="0" cellspacing="0"
        style="border: 1px solid #e5e7eb; border-collapse: collapse; font-size: 14px; margin-bottom: 24px;">
        <tr>
            <td
                style="padding: 11px 14px; background: #f9fafb; font-weight: 600; color: #374151; width: 160px; border-bottom: 1px solid #e5e7eb;">
                Order Number</td>
            <td style="padding: 11px 14px; border-bottom: 1px solid #e5e7eb;"><strong>#{{ $order->order_number }}</strong>
            </td>
        </tr>
        <tr>
            <td
                style="padding: 11px 14px; background: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">
                Customer</td>
            <td style="padding: 11px 14px; border-bottom: 1px solid #e5e7eb; line-height: 1.7;">
                {{ $order->customer_name }}<br>
                <a href="mailto:{{ $order->customer_email }}"
                    style="color: #1cab6a; text-decoration: none;">{{ $order->customer_email }}</a>
                @if($order->customer_phone)
                    <br>{{ $order->customer_phone }}
                @endif
            </td>
        </tr>
        @if($order->shipping_address)
            <tr>
                <td
                    style="padding: 11px 14px; background: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">
                    Shipping Address</td>
                <td style="padding: 11px 14px; border-bottom: 1px solid #e5e7eb; line-height: 1.7;">
                    {{ $order->shipping_address }}
                </td>
            </tr>
        @endif
        @if($order->status === 'shipped' && !empty($order->tracking_number))
            <tr>
                <td
                    style="padding: 11px 14px; background: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">
                    Tracking Number</td>
                <td style="padding: 11px 14px; border-bottom: 1px solid #e5e7eb; font-family: monospace;">
                    {{ $order->tracking_number }}
                </td>
            </tr>
        @endif
        <tr>
            <td
                style="padding: 11px 14px; background: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">
                Payment</td>
            <td style="padding: 11px 14px; border-bottom: 1px solid #e5e7eb; line-height: 1.7;">
                Method: {{ ucfirst($order->payment_method ?? 'N/A') }}<br>
                Status:
                <span style="color: {{ $order->payment_status === 'paid' ? '#059669' : '#d97706' }}; font-weight: 600;">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </td>
        </tr>
        <tr>
            <td style="padding: 11px 14px; background: #f9fafb; font-weight: 600; color: #374151;">Order Date</td>
            <td style="padding: 11px 14px;">{{ dateFormat($order->created_at) }}</td>
        </tr>
    </table>

    {{-- Ordered Items --}}
    <p style="margin: 0 0 10px; font-size: 15px; font-weight: 700; color: #111827;">Ordered Items</p>

    <table width="100%" cellpadding="0" cellspacing="0"
        style="border: 1px solid #e5e7eb; border-collapse: collapse; font-size: 14px; margin-bottom: 24px;">
        <tr style="background: #f9fafb;">
            <td style="padding: 10px 14px; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Product</td>
            <td
                style="padding: 10px 14px; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb; text-align: center; width: 60px;">
                Qty</td>
            <td
                style="padding: 10px 14px; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb; text-align: right; width: 90px;">
                Price</td>
            <td
                style="padding: 10px 14px; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb; text-align: right; width: 100px;">
                Subtotal</td>
        </tr>
        @foreach($order->items as $item)
            <tr>
                <td style="padding: 10px 14px; {{ !$loop->last ? 'border-bottom: 1px solid #f3f4f6;' : '' }} line-height: 1.6;">
                    {{ $item->product_name }}
                    @if($item->variant_name)
                        <br><span style="font-size: 12px; color: #6b7280;">{{ $item->variant_name }}</span>
                    @endif
                </td>
                <td
                    style="padding: 10px 14px; {{ !$loop->last ? 'border-bottom: 1px solid #f3f4f6;' : '' }} text-align: center; color: #374151;">
                    {{ $item->quantity }}
                </td>
                <td
                    style="padding: 10px 14px; {{ !$loop->last ? 'border-bottom: 1px solid #f3f4f6;' : '' }} text-align: right; color: #374151;">
                    {{  currencyformat($item->price)}}
                </td>
                <td
                    style="padding: 10px 14px; {{ !$loop->last ? 'border-bottom: 1px solid #f3f4f6;' : '' }} text-align: right; font-weight: 600;">
                    {{  currencyformat($item->subtotal)  }}
                </td>
            </tr>
        @endforeach
    </table>

    {{-- Financial Summary --}}
    <p style="margin: 0 0 10px; font-size: 15px; font-weight: 700; color: #111827;">Financial Summary</p>

    <table width="100%" cellpadding="0" cellspacing="0"
        style="border: 1px solid #e5e7eb; border-collapse: collapse; font-size: 14px;">
        <tr>
            <td style="padding: 10px 14px; color: #6b7280; border-bottom: 1px solid #f3f4f6;">Subtotal</td>
            <td style="padding: 10px 14px; text-align: right; border-bottom: 1px solid #f3f4f6;">
                ₹{{ currencyformat($order->subtotal) }}</td>
        </tr>
        @if($order->discount_total > 0)
            <tr>
                <td style="padding: 10px 14px; color: #6b7280; border-bottom: 1px solid #f3f4f6;">Discount</td>
                <td style="padding: 10px 14px; text-align: right; border-bottom: 1px solid #f3f4f6; color: #dc2626;">-
                    {{ currencyformat($order->discount_total) }}</td>
            </tr>
        @endif
        @if($order->tax_total > 0)
            <tr>
                <td style="padding: 10px 14px; color: #6b7280; border-bottom: 1px solid #f3f4f6;">Tax (GST)</td>
                <td style="padding: 10px 14px; text-align: right; border-bottom: 1px solid #f3f4f6;">
                    {{ currencyformat($order->tax_total) }}</td>
            </tr>
        @endif
        @if($order->shipping_total > 0)
            <tr>
                <td style="padding: 10px 14px; color: #6b7280; border-bottom: 1px solid #e5e7eb;">Shipping</td>
                <td style="padding: 10px 14px; text-align: right; border-bottom: 1px solid #e5e7eb;">
                    {{ currencyformat($order->shipping_total) }}</td>
            </tr>
        @endif
        <tr style="background: #f9fafb;">
            <td style="padding: 14px; font-size: 16px; font-weight: 700; color: #111827;">Total</td>
            <td style="padding: 14px; text-align: right; font-size: 16px; font-weight: 700; color: #1cab6a;">
                {{ currencyformat($order->total) }}</td>
        </tr>
    </table>

@endsection