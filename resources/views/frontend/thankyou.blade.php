@extends('layouts.frontend')

@section('meta')
    <x-frontend.meta :model="$page" />
@endsection

@section('content')
<section class="py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body text-center p-5">

                        <!-- Success Icon -->
                        <div class="mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10"
                                 style="width: 90px; height: 90px;">
                                <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                            </div>
                        </div>

                        <!-- Heading -->
                        <h1 class="fw-bold text-success mb-3">
                            Thank You!
                        </h1>

                        <!-- Message -->
                        <p class="text-muted fs-5 mb-4">
                            Your submission has been successfully received.
                            <br>
                            Our team will get back to you shortly.
                        </p>

                        @if(!empty($page->description))
                            <!-- <div class="mb-4 text-start">
                                {!! $page->description !!}
                            </div> -->
                        @endif

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 d-sm-flex justify-content-center">
                            <a href="{{ url('/') }}" class="btn btn-success px-4 py-2 rounded-pill fw-semibold">
                                Go to Home
                            </a>

                            <a href="{{ url('/contact') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-semibold">
                                Contact Support
                            </a>
                        </div>

                    </div>
                </div>

                <!-- Footer Help Text -->
                <p class="text-center text-muted small mt-4 mb-0">
                    Need help? Our support team is always here for you.
                </p>

            </div>
        </div>
    </div>
</section>
@endsection