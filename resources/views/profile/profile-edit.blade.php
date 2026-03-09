@extends('layouts.frontend')

@section('meta')
    {{-- <x-frontend-meta :model="$page" /> --}}
@endsection

@section('content')
    <!--  account section start here -->
    <section class="account-sec">
        <div class="container mt-4 mb-5">
            
            <div class="row">
                @include('profile.partials.sidebar')

                <!-- Main Content -->
                <div class="col-lg-9 col-md-8">
                    <div class="main-content">
                        <!-- Profile Page -->
                        <div id="profile-page" class="page-content">
                            <h2 class="page-title">My profile</h2>
                            <form method="POST" id="myForm" action="{{ route('profile.update') }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" id="name" name="name" class="form-control"
                                            value="{{ old('name', auth()->user()->name) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" id="email" name="email" class="form-control"
                                            value="{{ old('email', auth()->user()->email) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" name="phone" id="phone" class="form-control"
                                            value="{{ old('phone', auth()->user()->phone) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Profile Image</label>
                                        <input type="file" id="profile_image" name="profile_image" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label d-block">Account Status</label>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="status" value="1" {{ auth()->user()->status ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                {{ auth()->user()->status ? 'Active' : 'Inactive' }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary-custom">
                                    Update Info
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <x-frontend.intl-tel-input />
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            loadPhoneInput("#phone");

            const rule = [
                { selector: "#name", rule: "name" },
                { selector: "#email", rule: "email" },
                { selector: "#phone", rule: "phone" },
                { selector: "#profile_image", rule: "phone" },
            ];

            const validator = initFormValidator("#myForm", rule);

        });

    </script>
@endpush