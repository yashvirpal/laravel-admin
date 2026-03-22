{{-- resources/views/emails/orders/placed.blade.php --}}
@component('mail::message')
# Order Confirmed 🎉

Hi {{ $order->customer_name }},

Thank you for your order! Here's your summary:

**Order:** #{{ $order->order_number }}
**Date:** {{ $order->created_at->format('d M Y') }}
**Status:** {{ ucfirst($order->status) }}
**Payment:** {{ ucfirst($order->payment_status) }}

@component('mail::table')
| Product | Qty | Price |
|:--------|:---:|------:|
@foreach($order->items as $item)
| {{ $item->product_name }} {{ $item->variant_name ? '('.$item->variant_name.')' : '' }} | {{ $item->quantity }} | {{ currencyformat($item->subtotal) }} |
@endforeach
@endcomponent

| | |
|-|-|
| Subtotal | {{ currencyformat($order->subtotal) }} |
| Shipping | {{ $order->shipping_total > 0 ? currencyformat($order->shipping_total) : 'Free' }} |
| Discount | -{{ currencyformat($order->discount_total) }} |
| **Total** | **{{ currencyformat($order->total) }}** |

@component('mail::button', ['url' => route('profile.orders.show', $order->id)])
View Order
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent