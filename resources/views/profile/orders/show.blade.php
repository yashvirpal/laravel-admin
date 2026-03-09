@extends('layouts.frontend')

@section('content')
<section class="account-sec">
    <div class="container mt-4 mb-5">
        <!-- <h1 class="mb-4">My Account</h1> -->

        <div class="row">
            @include('profile.partials.sidebar')

            <div class="col-lg-9 col-md-8">
                <div class="main-content">

                    <div class="page-content">
                        <h2 class="page-title mb-4">
                            Order #{{ $order->id }}
                        </h2>

                        {{-- ================= ORDER SUMMARY ================= --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <strong>Order Summary</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Date:</strong> {{ dateFormat($order->created_at) }}</p>
                                        <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <p><strong>Total:</strong> {{ currencyformat($order->total) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================= PRODUCTS ================= --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <strong>Ordered Products</strong>
                            </div>

                            <div class="table-responsive">
                                <table class="table mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Variant</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td>
                                                    {{ $item->product->name ?? 'N/A' }}
                                                </td>

                                                <td>
                                                    {{ $item->variant->name ?? '-' }}
                                                </td>

                                                <td>{{ $item->quantity }}</td>

                                                <td>
                                                    {{ currencyformat($item->price) }}
                                                </td>

                                                <td>
                                                    {{ currencyformat($item->price * $item->quantity) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- ================= COUPONS ================= --}}
                        @if($order->coupons->count())
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <strong>Applied Coupons</strong>
                                </div>
                                <div class="card-body">
                                    @foreach($order->coupons as $coupon)
                                        <p>
                                            <strong>Code:</strong> {{ $coupon->code }} <br>
                                            <strong>Discount:</strong>
                                            {{ currencyformat($coupon->pivot->discount_amount ?? 0) }}
                                        </p>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- ================= TRANSACTIONS ================= --}}
                        @if($order->transactions->count())
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <strong>Payment Transactions</strong>
                                </div>
                                <div class="card-body">
                                    @foreach($order->transactions as $transaction)
                                        <div class="mb-3">
                                            <p>
                                                <strong>Transaction ID:</strong> {{ $transaction->transaction_id }} <br>
                                                <strong>Method:</strong> {{ $transaction->payment_method }} <br>
                                                <strong>Status:</strong> {{ ucfirst($transaction->status) }} <br>
                                                <strong>Amount:</strong> {{ currencyformat($transaction->amount) }}
                                            </p>
                                        </div>
                                        <hr>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <a href="{{ route('profile.orders') }}" 
                           class="btn btn-outline-secondary">
                            Back to Orders
                        </a>

                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection