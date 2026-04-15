@extends('layouts.frontend')

@section('meta')
    <x-frontend.meta :model="$page ?? null" />
@endsection

@section('content')
    <section class="order-status-sec">
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">

                    @if($order)

                        <div class="card shadow-sm border-0 rounded-4 p-5">

                            {{-- ✅ ICON --}}
                            <div class="mb-4">
                                @if($status === 'success')
                                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                                @endif
                            </div>

                            {{-- ✅ TITLE --}}
                            <h2 class="mb-3 {{ $status === 'success' ? 'text-success' : 'text-danger' }}">
                                {{ $status === 'success' ? 'Order Confirmed' : 'Order Failed' }}
                            </h2>

                            {{-- ✅ MESSAGE --}}
                            @if($status === 'success')
                                <p class="mb-2">Thank you for your order!</p>
                            @else
                                <p class="text-muted mb-2">
                                    {{ session('error') ?? 'Unfortunately, your order could not be processed.' }}
                                </p>
                            @endif

                            {{-- ✅ ORDER INFO --}}
                            <div class="alert alert-light border rounded-3 text-start my-4">
                                <p class="mb-1"><strong>Order ID:</strong> #{{ $order->id }}</p>
                                <p class="mb-1"><strong>Total:</strong> {{ currencyformat($order->total) }}</p>
                                <p class="mb-0">
                                    <strong>Status:</strong>
                                    <span class="badge {{ orderStatusBadge($order->status)['class'] }}">
                                        <i class="fa {{ orderStatusBadge($order->status)['icon'] }}"></i>
                                        {{ orderStatusBadge($order->status)['text'] }}
                                    </span>
                                </p>
                            </div>

                            {{-- ✅ SUCCESS EXTRA --}}
                            @if($status === 'success')
                                <p class="mb-4">
                                    Your order has been placed successfully and is being processed.
                                </p>
                            @endif

                            {{-- ❌ FAILED EXTRA --}}
                            @if($status === 'failed')
                                <div class="alert alert-light border rounded-3 text-start mb-4">
                                    <p class="mb-1"><strong>Common reasons for failure:</strong></p>
                                    <ul class="mb-0 ps-3 text-muted small">
                                        <li>Insufficient account balance</li>
                                        <li>Card declined by your bank</li>
                                        <li>Session timeout during payment</li>
                                        <li>Network issue during transaction</li>
                                    </ul>
                                </div>
                            @endif

                            {{-- ✅ ACTION BUTTONS --}}
                            <div class="d-flex justify-content-center gap-3 flex-wrap mt-3">

                                @if($status === 'failed')
                                    <a href="{{ route('page', 'checkout') }}" class="btn btn-danger rounded-pill px-4">
                                        <i class="fas fa-redo me-2"></i> Try Again
                                    </a>
                                @endif

                                <a href="{{ route('page', 'cart') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                    <i class="fas fa-shopping-cart me-2"></i> Cart
                                </a>

                                <a href="{{ url('/') }}" class="btn btn-outline-primary rounded-pill px-4">
                                    <i class="fas fa-home me-2"></i> Continue Shopping
                                </a>

                            </div>

                            {{-- SUPPORT --}}
                            <p class="text-muted small mt-4 mb-0">
                                Need help?
                                <a href="{{ route('page', 'contact-us') }}" class="text-decoration-none">
                                    Contact our support team
                                </a>
                            </p>

                        </div>

                    @else

                        <div class="alert alert-danger text-center">
                            Invalid or expired order link.
                        </div>

                    @endif

                </div>
            </div>
        </div>
    </section>
@endsection