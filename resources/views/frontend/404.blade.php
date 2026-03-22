@extends('layouts.frontend')

@section('meta')
    <x-frontend.meta :model="$page" />
@endsection

@section('content')

    <section class="contact-sec d-flex align-items-center" style="min-height: 80vh;">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-md-8">

                    <h1 style="font-size: 120px; font-weight: 700; color: #ff4d4f;">
                        404
                    </h1>

                    <h3 class="mb-3">Oops! Page Not Found</h3>

                    <p class="text-muted mb-4">
                        The page you are looking for might have been removed,
                        had its name changed, or is temporarily unavailable.
                    </p>

                    <a href="{{ url('/') }}" class="btn btn-primary px-4 py-2">
                        Go Back Home
                    </a>

                </div>
            </div>
        </div>
    </section>

@endsection