@extends('layouts.frontend')

@section('meta')
    {{-- <x-frontend-meta /> --}}
@endsection

@section('content')
    <!-- reset password section start here -->

    <section class="login-sec">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="login-container">

                        <div class="login-form">
                            <div class="login-form-inner">
                                <h1>Reset Password</h1>
                                <p class="mb-3 text-muted">
                                    Create a new password for your account.
                                </p>

                                <form method="POST" action="{{ route('password.store') }}" id="resetPasswordForm">
                                    @csrf

                                    <!-- Required by Laravel -->
                                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                                    <input type="hidden" name="email"
                                        value="{{ old('email', $request->ue ? decrypt($request->ue) : '') }}">

                                    <div class="login-form-group">
                                        <label for="password">New Password</label>
                                        <input type="password" name="password" id="password"
                                            placeholder="Minimum 8 characters" autocomplete="off">
                                        <small class="text-danger error_password error"></small>
                                    </div>

                                    <div class="login-form-group">
                                        <label for="password_confirmation">Confirm Password</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            placeholder="Confirm Password" autocomplete="off">
                                        <small class="text-danger error_password_confirmation error"></small>
                                    </div>

                                    <span class="my-1 msg"></span>

                                    <button type="submit" class="rounded-button login-cta">
                                        Reset Password
                                    </button>

                                    <div class="register-div mt-3">
                                        <a href="{{ route('login') }}" class="link create-account">
                                            Back to Login
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="login-img-wrap">
                            <figure>
                                <img src="{{ asset('frontend/assets/images/re.png') }}" alt="">
                            </figure>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- reset password section end here -->
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const rules = [
                { selector: "#password", rule: "password" },
                { selector: "#password_confirmation", rule: "password_confirmation" }
            ];

            setTimeout(() => {
                initFormValidator("#resetPasswordForm", rules);
            }, 500);
        });
    </script>
@endpush