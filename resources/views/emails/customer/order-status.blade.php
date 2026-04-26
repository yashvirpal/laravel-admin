@extends('emails.layouts.master')

@section('content')

    {{-- Greeting header --}}
    <table width="100%" cellpadding="0" cellspacing="0" stylee="background: #111827; margin-bottom: 0;">
        <tr>
            <td style="padding: 20px 28px;">
                <p style="margin: 0; font-size: 15px; font-weight: 600; colorr: #ffffff;">
                    Hi {{ $order->customer_name }},
                </p>
                <p style="margin: 4px 0 0; font-size: 12px; colorr: rgba(255,255,255,0.55);">
                    Your order has been updated
                </p>
            </td>
        </tr>
    </table>

    <div style="padding: 24px 28px;">

        {{-- Status badge --}}
        @php
            $statusMap = [
                'pending' => ['label' => 'Pending', 'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fcd34d', 'msg' => 'Your order has been received and is awaiting confirmation.'],
                'processing' => ['label' => 'Processing', 'color' => '#2563eb', 'bg' => '#eff6ff', 'border' => '#93c5fd', 'msg' => 'We are preparing your order.'],
                'shipped' => ['label' => 'Shipped', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'border' => '#c4b5fd', 'msg' => 'Your order is on the way. Check tracking below.'],
                'delivered' => ['label' => 'Delivered', 'color' => '#059669', 'bg' => '#ecfdf5', 'border' => '#6ee7b7', 'msg' => 'Your order has been delivered.'],
                'completed' => ['label' => 'Completed', 'color' => '#1cab6a', 'bg' => '#ecfdf5', 'border' => '#1cab6a', 'msg' => 'Thank you for shopping with us!'],
                'cancelled' => ['label' => 'Cancelled', 'color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fca5a5', 'msg' => 'Your order has been cancelled. Contact us if you need help.'],
                'returned' => ['label' => 'Returned', 'color' => '#9333ea', 'bg' => '#faf5ff', 'border' => '#d8b4fe', 'msg' => 'Your return is initiated. Refund will be processed shortly.'],
            ];
            $s = $statusMap[$order->status] ?? ['label' => ucfirst($order->status), 'color' => '#374151', 'bg' => '#f9fafb', 'border' => '#d1d5db', 'msg' => 'We will keep you updated.'];
        @endphp

        <div
            style="background: {{ $s['bg'] }}; border: 1px solid {{ $s['border'] }}; border-left: 3px solid {{ $s['color'] }}; padding: 14px 18px; margin-bottom: 20px;">
            <p
                style="margin: 0 0 2px; font-size: 11px; color: {{ $s['color'] }}; text-transform: uppercase; letter-spacing: 0.08em;">
                Order status</p>
            <p style="margin: 0 0 4px; font-size: 19px; font-weight: 700; color: {{ $s['color'] }};">{{ $s['label'] }}</p>
            <p style="margin: 0; font-size: 13px; color: {{ $s['color'] }};">{{ $s['msg'] }}</p>
        </div>

        {{-- Tracking number --}}
        @if($order->status === 'shipped' && !empty($order->tracking_number))
            <div
                style="background: #f5f3ff; border: 1px solid #c4b5fd; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px; text-align: center;">
                <p style="margin: 0 0 2px; font-size: 11px; color: #7c3aed; text-transform: uppercase; letter-spacing: 0.08em;">
                    Tracking number</p>
                <p style="margin: 0; font-size: 17px; font-weight: 700; font-family: monospace; color: #4c1d95;">
                    {{ $order->tracking_number }}</p>
            </div>
        @endif

        {{-- 2x2 info cards --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">
            <tr>
                <td width="50%" style="padding-right: 5px; padding-bottom: 10px; vertical-align: top;">
                    <div style="background: #f9fafb; border-radius: 6px; padding: 12px 14px;">
                        <p
                            style="margin: 0 0 3px; font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.06em;">
                            Order number</p>
                        <p style="margin: 0; font-size: 15px; font-weight: 700; color: #111827;">#{{ $order->order_number }}
                        </p>
                    </div>
                </td>
                <td width="50%" style="padding-left: 5px; padding-bottom: 10px; vertical-align: top;">
                    <div style="background: #f9fafb; border-radius: 6px; padding: 12px 14px;">
                        <p
                            style="margin: 0 0 3px; font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.06em;">
                            Order date</p>
                        <p style="margin: 0; font-size: 15px; font-weight: 700; color: #111827;">
                            {{ dateFormat($order->created_at) }}</p>
                    </div>
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding-right: 5px; vertical-align: top;">
                    <div style="background: #f9fafb; border-radius: 6px; padding: 12px 14px;">
                        <p
                            style="margin: 0 0 3px; font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.06em;">
                            Total paid</p>
                        <p style="margin: 0; font-size: 15px; font-weight: 700; color: #111827;">
                            {{ currencyformat($order->total) }}</p>
                    </div>
                </td>
                <td width="50%" style="padding-left: 5px; vertical-align: top;">
                    <div style="background: #f9fafb; border-radius: 6px; padding: 12px 14px;">
                        <p
                            style="margin: 0 0 3px; font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.06em;">
                            Payment</p>
                        <p
                            style="margin: 0; font-size: 15px; font-weight: 700; color: {{ $order->payment_status === 'paid' ? '#059669' : '#d97706' }};">
                            {{ ucfirst($order->payment_status) }}
                        </p>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Items --}}
        <p
            style="margin: 0 0 10px; font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.06em; border-top: 1px solid #f3f4f6; padding-top: 16px;">
            Items ordered</p>

        @foreach($order->items as $item)
            <table width="100%" cellpadding="0" cellspacing="0"
                style="{{ !$loop->last ? 'border-bottom: 1px solid #f3f4f6; margin-bottom: 8px; padding-bottom: 8px;' : '' }}">
                <tr>
                    <td style="vertical-align: top;">
                        <p style="margin: 0; font-size: 14px; color: #111827;">{{ $item->product_name }}</p>
                        <p style="margin: 2px 0 0; font-size: 12px; color: #6b7280;">
                            @if($item->variant_name){{ $item->variant_name }} &middot; @endif
                            Qty {{ $item->quantity }} &middot; {{ currencyformat($item->price) }}
                        </p>
                    </td>
                    <td style="vertical-align: top; text-align: right; white-space: nowrap;">
                        <p style="margin: 0; font-size: 14px; font-weight: 700; color: #111827;">
                            {{ currencyformat($item->price) }}</p>
                    </td>
                </tr>
            </table>
        @endforeach

        {{-- CTA --}}
        <div style="margin-top: 24px; text-align: center;">
            <a href="{{ route('profile.orders.show', $order->id) }}"
                style="display: inline-block; padding: 12px 32px; background: #111827; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; letter-spacing: 0.02em;">
                View order details
            </a>
        </div>

    </div>

@endsection