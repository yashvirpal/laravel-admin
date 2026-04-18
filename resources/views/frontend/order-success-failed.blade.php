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
                                <div class="card border-0 shadow-sm rounded-3">
                                    <div class="card-body p-4">

                                        {{-- Header: Order ID + Status Badge --}}
                                        <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                                            <div>
                                                <p class="text-muted small mb-0">Order</p>
                                                <h6 class="fw-semibold mb-0">#{{ $order->id }}</h6>
                                            </div>
                                            <span
                                                class="badge rounded-pill {{ orderStatusBadge($order->status)['class'] }} px-3 py-2">
                                                <i class="fa {{ orderStatusBadge($order->status)['icon'] }} me-1"></i>
                                                {{ orderStatusBadge($order->status)['text'] }}
                                            </span>
                                        </div>

                                        {{-- Details --}}
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted small">Date</span>
                                            <span class="small fw-medium">{{dateFormat($order->created_at) }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="text-muted small">Items</span>
                                            <span class="small fw-medium">{{ count($order->items) }} products</span>
                                        </div>

                                        {{-- Total --}}
                                        <div class="d-flex justify-content: between align-items-center pt-3 border-top">
                                            <span class="fw-semibold">Total</span>
                                            <span
                                                class="fw-semibold text-primary fs-6 ms-auto">{{ currencyformat($order->total) }}</span>
                                        </div>

                                    </div>
                                </div>
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
                                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                                        <div class="card-body p-4">

                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-warning-subtle text-warning rounded-circle p-2 me-2">
                                                    <i class="fa fa-exclamation-triangle"></i>
                                                </div>
                                                <h6 class="mb-0 fw-semibold">Common Reasons for Payment Failure</h6>
                                            </div>

                                            <div class="row g-2">

                                                <div class="col-12">
                                                    <div class="d-flex align-items-start">
                                                        <i class="fa fa-circle text-muted mt-1 me-2" style="font-size:6px;"></i>
                                                        <span class="text-muted small">Insufficient account balance</span>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="d-flex align-items-start">
                                                        <i class="fa fa-circle text-muted mt-1 me-2" style="font-size:6px;"></i>
                                                        <span class="text-muted small">Card declined by your bank</span>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="d-flex align-items-start">
                                                        <i class="fa fa-circle text-muted mt-1 me-2" style="font-size:6px;"></i>
                                                        <span class="text-muted small">Session timeout during payment</span>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="d-flex align-items-start">
                                                        <i class="fa fa-circle text-muted mt-1 me-2" style="font-size:6px;"></i>
                                                        <span class="text-muted small">Network issue during transaction</span>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(request('failed') && in_array($order->payment_status, ['failed', 'pending']))
                                <!-- <div class="mt-4">
                                                                                                                                                                                                            <p class="text-red-600 text-sm mb-2">Your payment was not completed.</p>
                                                                                                                                                                                                            <button id="retry-payment-btn" data-order="{{ encrypt($order->id) }}"
                                                                                                                                                                                                                class="bg-blue-600 text-white px-4 py-2 rounded text-sm">
                                                                                                                                                                                                                Retry Payment
                                                                                                                                                                                                            </button>
                                                                                                                                                                                                        </div> -->


                            @endif

                            {{-- ✅ ACTION BUTTONS --}}
                            <div class="d-flex justify-content-center gap-3 flex-wrap mt-3">

                                @if($status === 'failed')
                                    <button id="retry-payment-btn" data-order="{{ encrypt($order->id) }}"
                                        class="btn btn-danger rounded-pill px-4">
                                        <i class="fas fa-redo me-2"></i> Try Again
                                    </button>
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
@push('scripts')
    <script>
        document.getElementById('retry-payment-btn').addEventListener('click', function () {
            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Processing...';

            fetch('{{ route('payment.retry') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: btn.dataset.order })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        alert(data.message || 'Payment failed. Please try again.');
                        btn.disabled = false;
                        btn.textContent = 'Retry Payment';
                    }
                })
                .catch(() => {
                    alert('Something went wrong.');
                    btn.disabled = false;
                    btn.textContent = 'Retry Payment';
                });
        });
    </script>
@endpush