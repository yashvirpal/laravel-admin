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

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2 class="page-title mb-0">Addresses</h2>

                                <a href="{{ route('profile.addresses.create') }}" class="btn btn-primary btn-sm">
                                    + Add New Address
                                </a>
                            </div>

                            <div class="row">
                                @forelse($addresses as $address)
                                    <div class="col-md-6 mb-4">
                                        <x-frontend.user.address-list-card :item="$address" />
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="card text-center p-4">
                                            <p class="mb-2 text-muted">
                                                You have not added any address yet.
                                            </p>
                                            <a href="{{ route('addresses.create') }}" class="btn btn-outline-primary btn-sm">
                                                Add Your First Address
                                            </a>
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const rules = [];

            document.querySelectorAll(".delete-address-form").forEach(form => {
                initFormValidator("#" + form.id, rules);
            });
        });
    </script>
@endpush