{{-- resources/views/emails/orders/status-updated.blade.php --}}
@component('mail::message')
# Order Status Updated

Hi {{ $order->customer_name }},

Your order **#{{ $order->order_number }}** status has been updated.

**New Status:** {{ ucfirst($order->status) }}
**Payment Status:** {{ ucfirst($order->payment_status) }}

@component('mail::button', ['url' => route('profile.orders.show', $order->id)])
View Order
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent