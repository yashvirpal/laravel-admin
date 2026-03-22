@extends('layouts.frontend')

@section('meta')
    <x-frontend.meta :model="$page ?? null" />
@endsection

@section('content')
    <section class="order-fail-sec">
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">

                    <div class="card shadow-sm border-0 rounded-4 p-5">

                        <div class="mb-4">
                            <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                        </div>

                        <h2 class="text-danger mb-3">Order Failed</h2>

                        <p class="text-muted mb-2">
                            @if(session('error'))
                                {{ session('error') }}
                            @else
                                Unfortunately, your order could not be processed.
                            @endif
                        </p>

                        <p class="text-muted mb-4">
                            Don't worry — no amount has been deducted. Please try again or use a different payment method.
                        </p>

                        <div class="alert alert-light border rounded-3 text-start mb-4">
                            <p class="mb-1"><strong>Common reasons for failure:</strong></p>
                            <ul class="mb-0 ps-3 text-muted small">
                                <li>Insufficient account balance</li>
                                <li>Card declined by your bank</li>
                                <li>Session timeout during payment</li>
                                <li>Network issue during transaction</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-center gap-3 flex-wrap mt-2">
                            <a href="{{ route('page', 'checkout') }}" class="btn btn-danger rounded-pill px-4">
                                <i class="fas fa-redo me-2"></i> Try Again
                            </a>
                            <a href="{{ route('page', 'cart') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-shopping-cart me-2"></i> Back to Cart
                            </a>
                            <a href="{{ url('/') }}" class="btn btn-outline-primary rounded-pill px-4">
                                <i class="fas fa-home me-2"></i> Continue Shopping
                            </a>
                        </div>

                        <p class="text-muted small mt-4 mb-0">
                            Need help?
                            <a href="{{ route('page', 'contact-us') }}" class="text-decoration-none">Contact our support
                                team</a>
                        </p>

                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection