@extends('layouts.frontend')

@section('meta')
    {{-- <x-frontend-meta :model="$page" /> --}}
@endsection

@section('content')
    <section class="contact-sec">
        <div class="container my-5">

            @php


                try {
                    $orderId = decrypt(request()->order);
                    $order = \App\Models\Order::find($orderId);
                } catch (\Exception $e) {
                    $order = null;
                }
            @endphp

            <div class="row justify-content-center">
                <div class="col-md-8 text-center">

                    @if($order)
                        <div class="card shadow-sm border-0 rounded-4 p-4">
                            <h2 class="text-success mb-3">
                                <i class="fas fa-check-circle"></i> Order Confirmed
                            </h2>

                            <p class="mb-2">
                                Thank you for your order!
                            </p>

                            <p class="mb-2">
                                <strong>Order ID:</strong> #{{ $order->id }}
                            </p>

                            <p class="mb-4">
                                We have received your order and it is being processed.
                            </p>

                            <div class="text-center mt-4">
                                <a href="{{ url('/') }}" class="btn btn-primary rounded-pill px-4">
                                    Continue Shopping
                                </a>
                            </div>
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