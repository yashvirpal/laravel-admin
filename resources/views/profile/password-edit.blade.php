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
                            <h2 class="page-title">Update Password</h2>
                            <form method="POST" id="myForm" action="{{ route('profile.password.update') }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row justify-content-center">
                                    <div class="col-md-5">

                                        <div class="mb-3">
                                            <label class="form-label">Current Password</label>
                                            <input type="password" id="current_password" name="current_password"
                                                class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">New Password</label>
                                            <input type="password" id="password" name="password" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Confirm Password</label>
                                            <input type="password" id="password_confirmation" name="password_confirmation"
                                                class="form-control">
                                        </div>

                                        <button type="submit" class="btn btn-primary-custom w-100 mybtn"> Update </button>

                                    </div>
                                </div>

                            </form>
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
            const rule = [
                { selector: "#password", rule: "password" },
                { selector: "#current_password", rule: "password" },
                { selector: "#password_confirmation", rule: "password_confirmation" },
            ];
            const validator = initFormValidator("#myForm", rule);
        });
    </script>
@endpush