{{-- resources/views/emails/orders/admin.blade.php --}}
@component('mail::message')
# New Order Received 🛒

**Order:** #{{ $order->order_number }}
**Customer:** {{ $order->customer_name }} ({{ $order->customer_email }})
**Phone:** {{ $order->customer_phone ?? 'N/A' }}
**Payment Method:** {{ ucfirst($order->payment_method) }}
**Payment Status:** {{ ucfirst($order->payment_status) }}
**Total:** {{ currencyformat($order->total) }}

@component('mail::button', ['url' => route('admin.orders.show', $order->id)])
View in Admin
@endcomponent

{{ config('app.name') }}
@endcomponent